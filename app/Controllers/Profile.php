<?php

namespace App\Controllers;

use App\Models\UserModel;

class Profile extends BaseController
{
    public $session;

    public function index() {
        $session = session();
        $userModel = model('UserModel');
        $email_auto = $session->get('email') ?: get_cookie('email');
        $hashed_pw_auto = $session->get('hashed_pw') ?: get_cookie('hashed_pw');
        if (!$email_auto || $hashed_pw_auto !== $userModel->getHashedPassword($email_auto)) {
            return redirect()->to(base_url('login'));
        }
        
        $userData = $userModel->getUser($email_auto);
        
        echo view('logged_in_header');
        return view('profile', $userData);
    }

    public function update() {
        $session = session();
        $userModel = model('UserModel');
        $email_auto = $session->get('email') ?: get_cookie('email');
        $data = $userModel->getUser($email_auto);

        // update profile pic
        if (isset($_POST['save_pic'])) {
            $profilePic = $this->request->getFile('profile_pic');
            if (!$profilePic) {
                echo view('logged_in_header');
                echo '<p>No picture chosen<p>';
                return view('profile', $data);
            }
            $newImgName = $profilePic->getRandomName();
            $profilePic->move(FCPATH.'images/profile', $newImgName);
            $userModel->addPicture($email_auto, $newImgName);
            $data['profile_pic'] = $newImgName;
        }

        if (isset($_POST['rotate_pic'])) {
            $data['profile_pic'] = $userModel->rotatePicture($email_auto);
        }

        // update other info
        if (isset($_POST['save_details'])) {
            $data['first_name'] = $this->request->getPost('fname');
            $data['last_name'] = $this->request->getPost('lname');

            $userModel->updateDetails($session->get('email'), ['first_name' => $data['first_name'], 'last_name' => $data['last_name']]);
        }

        echo view('logged_in_header');
        echo "<p>Successfully updated</p>";
        return view('profile', $data);
    }

    public function changePassword() {
        if (! $this->request->is('post')) {
            echo view('logged_in_header');
            return view('change_password');
        }

        $session = session();
        $userModel = model('UserModel');

        $currPassword = $this->request->getPost('curr_password');
        $newPassword = $this->request->getPost('new_password');

        // validation
        $rules = [
            'new_password'   => 'required|min_length[8]',
            'confirm_new_pw' => 'required|matches[new_password]',
        ];
        if (! $userModel->login($session->get('email'), $currPassword)) {
            echo "<p>Current password does not match</p>";
            echo view('logged_in_header');
            return view('change_password');
        } else if (! $this->validate($rules)) {
            echo "<p>Invalid</p>";
            echo view('logged_in_header');
            return view('change_password');
        }

        // update
        $userModel->changePassword($session->get('email'), $newPassword);
        $session->set('hashed_pw', $userModel->getHashedPassword($session->get('email')));
        
        echo "<p>Password has been successfully changed</p>";
        echo view('logged_in_header');
        return view('change_password');
    }
}