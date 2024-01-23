<?php
namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'User';
    protected $primaryKey = 'id';
    protected $allowedFields = ['username', 'email', 'password', 'first_name', 'last_name', 'user_type', 'profile_pic', 'activated'];
    protected $validationRules = [
        'email'      => 'required|valid_email',
        'username'   => 'required|min_length[4]|alpha_numeric',
        'first_name' => 'required|alpha_numeric',
        'last_name'  => 'required|alpha_numeric',
        'password'   => 'required|min_length[8]',
        'user_type'  => 'required|alpha_numeric',
    ];

    public function login($email, $password) {
        $userRecord = $this->where('email', $email)->first();
        if ($userRecord) {
            return password_verify($password, $userRecord['password']);
        } else {
            return false;
        }
    }

    public function checkEmail($email) {
        $userRecord = $this->where('email', $email)->first();
        return $userRecord ? true : false;
    }

    public function getHashedPassword($email) {
        $userRecord = $this->where('email', $email)->first();
        return $userRecord ? $userRecord['password'] : NULL;     // hashed pw
    }
    
    public function register($details) {
        $details['password'] = password_hash($details['password'], PASSWORD_DEFAULT);
        return $this->insert($details, false);
    }

    public function activate($email) {
        $this->where('email', $email)->set(['activated' => true])->update();
    }

    public function getUser($email) {
        return $this->where('email', $email)->first();
    }
    
    public function changePassword($email, $password) {
        $hashed_pw = password_hash($password, PASSWORD_DEFAULT);
        $this->where('email', $email)->set(['password' => $hashed_pw])->update();
    }

    public function updateDetails($email, $data) {
        $this->where('email', $email)->set($data)->update();
    }

    public function getForumData($email, $subjectID, $postID = '') {
        $subjectModel = model('SubjectModel');
        $enrollModel = model('EnrollmentModel');
        $postModel = model('PostModel');
        $postImageModel = model('PostImageModel');
        $categoryModel = model('CategoryModel');
        $commentModel = model('CommentModel');

        // Get user data
        $userData = $this->getUser($email);
        // Get enrollments data
        $enrollmentsRaw = $enrollModel->getUserEnrollments($userData['id']);
        foreach ($enrollmentsRaw as $enrollment) {
            $id = $enrollment['id'];
            $subjID = $enrollment['subject_id'];
            $enrollments[$id]['subject_id'] = $subjID;
            $enrollments[$id]['subject_name'] = $subjectModel->find($subjID)['name'];
        }

        $data = [
            'userData'    => $userData,
            'enrollments' => $enrollments,
            'subjectID'   => $subjectID,
            'subject'     => $subjectModel->find($subjectID),
            'categories'  => $categoryModel->where('subject_id', $subjectID)->findAll(),
            'postID'      => $postID,
            'postData'    => $postModel->find($postID),
            'postImages'  => $postImageModel->getPostImages($postID),
            'allPosts'    => $postModel->where('subject_id', $subjectID)->findAll(),
            'postDraft'   => [],
            'comments'    => $commentModel->getPostComments($postID),
        ];

        return $data;
    }

    public function addPicture($email, $imgName) {
        return $this->where('email', $email)->set(['profile_pic' => $imgName])->update();
    }

    public function rotatePicture($email, $degrees = 90) {
        $imgName = $this->select('profile_pic')->where('email', $email)->first()['profile_pic'];
        $file = new \CodeIgniter\Files\File(FCPATH . 'images/profile/' . $imgName);
        $newImgName = $file->getRandomName();

        $imagick = new \Imagick(FCPATH . 'images/profile/' . $imgName);
        $imagick->rotateImage(new \ImagickPixel(), $degrees);
        $imagick->writeImage(FCPATH . 'images/profile/' . $newImgName);
        $imagick->clear();
        $imagick->destroy();
        unlink(FCPATH . 'images/profile/' . $imgName);

        $this->where('email', $email)->set(['profile_pic' => $newImgName])->update();

        return $newImgName;
    }
}