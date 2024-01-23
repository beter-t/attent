<?php

namespace App\Controllers;

use App\Models\UserModel;

class Test extends BaseController
{
    public $session;

    public function index() {
        $session = session();
        $userModel = model('UserModel');
        $subjectModel = model('SubjectModel');
        $enrollModel = model('EnrollmentModel');
        $postModel = model('PostModel');

        
        $email_auto = $session->get('email') ?: get_cookie('email');
        $hashed_pw_auto = $session->get('hashed_pw') ?: get_cookie('hashed_pw');
        
        /*
        echo '<pre>'; var_dump($_COOKIE); echo '</pre><br>';
        echo '<pre>'; var_dump($_SESSION); echo '</pre><br>';

        echo var_dump($email_auto);
        echo var_dump($hashed_pw_auto);

        //echo $hashed_pw_auto === $userModel->getHashedPassword($email_auto);
        echo $email_auto ? 'true' : 'false';
        */
        // $userData = $userModel->getUser($email_auto);
        // echo '<pre>'; var_dump($enrollModel->getUserEnrollments($userData['id'])); echo '</pre><br>';
        //$infs3202Posts = $postModel->where('subject_id', '1')->findAll();
        //$infs3202Posts[] = ['test' => 'test array push'];
        //echo '<pre>'; var_dump($infs3202Posts); echo '</pre><br>';
        $data = [
            'isChecked' => false,
        ];

        $pfp = $userModel->select('profile_pic')->where('email', $email_auto)->first()['profile_pic'];
        echo $pfp;
        return view('test', $data);
    }

    public function postreq() {
        if ($this->request->is('post')) {
            // echo '<pre>'; var_dump($_POST); echo '</pre><br>';
            if (isset($_POST['text1_btn'])) {
                echo "Text1 submitted";
                echo $this->request->getPost('text1');
            }
            if (isset($_POST['text2_btn'])) {
                echo "Text2 submitted";
                echo $this->request->getPost('text2');
            }
        }
        return view('test');
    }
}