<?php

namespace App\Controllers;

use CodeIgniter\Email\Email;

class Forum extends BaseController
{
    public $session;

    public function index($subjectID = '', $postID = '') {
        $session = session();
        $userModel = model('UserModel');
        $email_auto = $session->get('email') ?: get_cookie('email');
        
        $forumData = $userModel->getForumData($email_auto, $subjectID, $postID);
        if ($postID) {
            $bookmarkModel = model('BookmarkModel');
            $forumData['postData']['bookmarked'] = $bookmarkModel->hasBookmark($email_auto, $postID);

            $postLikesModel = model('PostLikesModel');
            $forumData['postData']['liked'] = $postLikesModel->hasLiked($email_auto, $postID);
            $forumData['postData']['likes'] = $postLikesModel->totalLikes($postID);
        }
        
        echo view('logged_in_header');
        return view('forum', $forumData);
    }

    public function suggest($subjectID, $str) {
        if ($this->request->isAJAX()) {
            $session = session();
            $userModel = model('UserModel');
            $postModel = model('PostModel');
            
            $search = $this->request->getPost('search');
            $results = $postModel->where('subject_id', $subjectID)->like('title', $str)->orLike('body', $str)->findAll();

            header('Content-Type: application/json');
            echo json_encode($results);
        }
    }

    public function action($subjectID, $postID = '') {
        if (isset($_POST['submit_post'])) {
            return $this->uploadPost($subjectID);
        } else if (isset($_POST['search_posts'])) {
            return $this->search($subjectID, $postID);
        } else if (isset($_POST['cancel_search'])) {
            return $this->index($subjectID, $postID);
        } else if (isset($_POST['comment_btn'])) {
            return $this->uploadComment($subjectID, $postID);
        }
    }

    public function bookmark($subjectID, $postID) {
        $session = session();
        $bookmarkModel = model('BookmarkModel');
        $email_auto = $session->get('email') ?: get_cookie('email');
        $bookmarkModel->toggle($email_auto, $postID);
    }

    public function like($subjectID, $postID) {
        $session = session();
        $postLikesModel = model('PostLikesModel');
        $email_auto = $session->get('email') ?: get_cookie('email');
        $postLikesModel->toggle($email_auto, $postID);
    }

    public function search($subjectID, $postID = '') {
        $session = session();
        $userModel = model('UserModel');
        $postModel = model('PostModel');
        
        
        $search = $this->request->getPost('search');
        $results = $postModel->where('subject_id', $subjectID)->like('title', $search)->orLike('body', $search)->findAll();

        $forumData = $userModel->getForumData($session->get('email'), $subjectID, $postID);
        if ($postID) {
            $bookmarkModel = model('BookmarkModel');
            $forumData['postData']['bookmarked'] = $bookmarkModel->hasBookmark($email_auto, $postID);

            $postLikesModel = model('PostLikesModel');
            $forumData['postData']['liked'] = $postLikesModel->hasLiked($email_auto, $postID);
            $forumData['postData']['likes'] = $postLikesModel->totalLikes($postID);
        }
        $forumData['allPosts'] = $results;

        echo view('logged_in_header');
        return view('forum', $forumData);
    }

    public function uploadPost($subjectID) {
        $session = session();
        $userModel = model('UserModel');
        $postModel = model('PostModel');
        $categoryModel = model('CategoryModel');

        // Get user data
        $userData = $userModel->getUser($session->get('email'));
        // Get post data
        $category = $this->request->getPost('category');
        $post = [
            'title' => $this->request->getPost('title') ?: 'Untitled',
            'body' => $this->request->getPost('body') ?: '',
            'post_type' => $this->request->getPost('post_type'),
            'public' => $this->request->getPost('public') ? true : false,
            'subject_id' => $subjectID,
            'category_id' => $categoryModel->where('name', $category)->first()['id'],
            'anonymous' => $this->request->getPost('anonymous') ? true : false,
            'category' => $this->request->getPost('category'),
            'user_id' => $userData['id'],
            'views' => 0,
        ];

        $postModel->addPost($post);
        $postID = $postModel->getInsertID();

        $postImages = $this->request->getFileMultiple('images');
        if ($postImages) {
            $rules = [
                'images' => 'uploaded[images]|max_size[images,2048]|ext_in[images,jpg,jpeg,png,gif]',
            ];
            if (! $this->validate($rules)) {
                echo view('logged_in_header');
                $forumData = $userModel->getForumData($userData['email'], $subjectID);
                $forumData['postDraft'] = $post;
                return view('forum', $forumData);
            }

            $postImageModel = model('PostImageModel');
            foreach ($postImages as $image) {
                $newImgName = $image->getRandomName();
                $image->move(FCPATH.'images/post', $newImgName);
                $postImageModel->addImagePath($postID, $newImgName);
            }
        }

        return redirect()->to(base_url('forum/'.$subjectID.'/'. $postID ?: ''));
    }

    public function uploadComment($subjectID, $postID) {
        $session = session();
        $userModel = model('UserModel');
        $email_auto = $session->get('email') ?: get_cookie('email');
        $commentModel = model('CommentModel');

        $userData = $userModel->getUser($email_auto);
        $comment = [
            'body' => $this->request->getPost('comment_draft'),
            'post_id' => $postID,
            'user_id' => $userData['id'],
        ];

        $commentModel->addComment($comment);

        $bookmarkModel = model('BookmarkModel');
        $bookmarkers = $bookmarkModel->getBookmarkers($postID);
        foreach ($bookmarkers as $bookmarker) {
            $uid = $bookmarker['user_id'];
            $uEmail = $userModel->find($uid)['email'];
            
            // attempt email
            $emailer = new Email();
            $emailerConfig = [
                'protocol'  => 'smtp',
                'wordWrap'  => true,
                'SMTPHost'  => 'mailhub.eait.uq.edu.au',
                'SMTPPort'  => 25,
            ];
            $emailer->initialize($emailerConfig);
            $emailer->setTo($uEmail);
            $emailer->setFrom('s4641616@student.uq.edu.au');
            $emailer->setSubject('Attent - Email Verification');
            $emailer->setMessage('Bookmarked post ' . $postID . ' has a new update');

            if (! $emailer->send()) {
                echo "Attempt to send email failed.";
            }
        }
        return redirect()->to(base_url('forum/'.$subjectID.'/'. $postID));
    }
}