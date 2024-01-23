<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Email\Email;

class Registration extends BaseController
{
    public $session;

    public function index() {
        $session = session();
        $userModel = model('UserModel');
        $email_auto = $session->get('email') ?: get_cookie('email');
        $hashed_pw_auto = $session->get('hashed_pw') ?: get_cookie('hashed_pw');
        if ($email_auto && $hashed_pw_auto === $userModel->getHashedPassword($email_auto)) {
            $session->set('email', $email_auto);
            $session->set('hashed_pw', $hashed_pw_auto);
            return redirect()->to(base_url('dashboard'));
        }

        return view('registration');
    }

    public function register() {
        if (! $this->request->is('post')) {
            return view('registration');
        }

        $session = session();

        $signupDetails = [
            'email'       => $this->request->getPost('email'),
            'username'    => $this->request->getPost('username'),
            'first_name'  => $this->request->getPost('fname'),
            'last_name'   => $this->request->getPost('lname'),
            'password'    => $this->request->getPost('password'),
            'user_type'   => $this->request->getPost('is_instructor') ?: 'student',
            'activated'   => false,
        ];
        
        // validation
        $rules = [
            'email'      => 'required|valid_email',
            'username'   => 'required|min_length[4]|alpha_numeric',
            'fname'      => 'required|alpha_numeric',
            'lname'      => 'required|alpha_numeric',
            'password'   => 'required|min_length[8]',
            'confirm_pw' => 'required|matches[password]',
        ];
        if (! $this->validate($rules)) {
            echo "Invalid";
            return view('registration');
        }
        $userModel = model('UserModel');
        if ($userModel->checkEmail($signupDetails['email'])) {
            echo "Email already in use";
            return view('registration');
        }

        // attempt email
        $session->set('verifCode', substr(md5(uniqid()), 0, 6));
        $emailer = new Email();
        $emailerConfig = [
            'protocol'  => 'smtp',
            'wordWrap'  => true,
            'SMTPHost'  => 'mailhub.eait.uq.edu.au',
            'SMTPPort'  => 25,
        ];
        $emailer->initialize($emailerConfig);
        $emailer->setTo($signupDetails['email']);
        $emailer->setFrom('s4641616@student.uq.edu.au');
        $emailer->setSubject('Attent - Email Verification');
        $emailer->setMessage("
        Thank you for signing up at Attent.
        Please enter the below code to activate your newly created account.
        Code: {$session->get('verifCode')}

        Note: this code will expire at the end of your browser session
        ");
        if (! $emailer->send()) {
            $session->remove('verifCode');
            echo "Attempt to send email failed.";
            return view ('registration');
        }
        echo view('verify_email');

        // register details; awaiting email verification
        if ($userModel->register($signupDetails)) {
            $session->set('email', $signupDetails['email']);
            $session->set('hashed_pw', $userModel->getHashedPassword($signupDetails['email']));
        } else echo "Error occurred during record insertion to database";
    }

    public function verify() {
        if (! $this->request->is('post')) {
            return view('verify_email');
        }
        $session = session();
        $inputCode = $this->request->getPost('verif_code');
        if ($inputCode == $session->get('verifCode')) {
            $session->remove('verifCode');
            $userModel = model('UserModel');
            $userModel->activate($session->get('email'));
            return redirect()->to(base_url('dashboard'));
        }
    }
}