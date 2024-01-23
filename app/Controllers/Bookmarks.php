<?php

namespace App\Controllers;

use App\Models\UserModel;

class Bookmarks extends BaseController
{
    public $session;

    public function index() {
        $session = session();
        $userModel = model('UserModel');
        $enrollModel = model('EnrollmentModel');
        $subjectModel = model('SubjectModel');
        $postModel = model('PostModel');
        $bookmarkModel = model('BookmarkModel');
        
        // Check login
        $email_auto = $session->get('email') ?: get_cookie('email');
        $hashed_pw_auto = $session->get('hashed_pw') ?: get_cookie('hashed_pw');
        if (!$email_auto || $hashed_pw_auto !== $userModel->getHashedPassword($email_auto)) {
            return redirect()->to(base_url('login'));
        }
        
        // Get user's enrolled subjects
        $userData = $userModel->getUser($session->get('email'));
        $enrollmentsRaw = $enrollModel->getUserEnrollments($userData['id']);
        $enrollments = [];
        foreach ($enrollmentsRaw as $enrollment) {
            $enrollments[$enrollment['id']] = [
                'id'   => $enrollment['subject_id'], 
                'name' => $subjectModel->find($enrollment['subject_id'])['name'],
            ];
        }

        // Get user's bookmarks
        $bookmarks = $bookmarkModel->getBookmarks($email_auto);
        foreach ($enrollments as $enrollment) {
            foreach ($bookmarks as $bookmark) {
                $bookmarksPerSubject[$enrollment['id']] = [];
                if ($postModel->inSubject($bookmark['post_id'], $enrollment['id'])) {
                    $bookmarksPerSubject[$enrollment['id']][] = $postModel->find($bookmark['post_id']);
                }
            }
        }
        
        echo view('logged_in_header');
        return view('bookmarks', ['enrollments' => $enrollments, 'bookmarks' => $bookmarksPerSubject]);
    }
}