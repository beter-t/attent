<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Email\Email;

class Authentication extends BaseController
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

        return view('login');
    }

    public function login() {
        $session = session();
        $userModel = model('UserModel');
        $email_auto = $session->get('email') ?: get_cookie('email');
        $hashed_pw_auto = $session->get('hashed_pw') ?: get_cookie('hashed_pw');
        if ($email_auto && $hashed_pw_auto === $userModel->getHashedPassword($email_auto)) {
            $session->set('email', $email_auto);
            $session->set('hashed_pw', $hashed_pw_auto);
            return redirect()->to(base_url('dashboard'));
        }
        
        // login submission
        if ($this->request->is('post')) {
            $email = $this->request->getPost('email');
            $password = $this->request->getPost('password');
            $remember = $this->request->getPost('remember');
            
            if ($userModel->login($email, $password)) {
                $hashed_pw = $userModel->getHashedPassword($email);
                $session->set('email', $email);
                $session->set('hashed_pw', $hashed_pw);

                $redirectResp = redirect()->to(base_url('dashboard'));
                if ($remember) {
                    $redirectResp->setCookie('email', $email, time() + 3600)
                    ->setCookie('hashed_pw', $hashed_pw, time() + 3600);
                }
                return $redirectResp;
            }
            echo "<p>Login details are incorrect</p>";
        }
        return view('login');
    }

    public function logout() {
        $session = session();
        $session->destroy();
        // destroy the cookies
        return redirect()->to(base_url('login'))
        ->deleteCookie('email')
        ->deleteCookie('hashed_pw');
    }

    public function forgotPassword() {
        if (! $this->request->is('post')) {
            return view('forgot_password');
        }

        $session = session();
        $session->set('resetToken', substr(md5(uniqid()), 0, 8));
        $session->set('emailToChangePassword', $this->request->getPost('email'));

        // validation
        $rules = [
            'email'      => 'required|valid_email',
        ];
        if (! $this->validate($rules)) {
            echo "<p>Invalid</p>";
            return view('forgot_password');
        }
        $userModel = model('UserModel');
        if (! $userModel->checkEmail($session->get('emailToChangePassword'))) {
            echo "<p>Account with email does not exist</p>";
            return view('forgot_password');
        }

        $emailer = new Email();
        $emailerConfig = [
            'protocol'  => 'smtp',
            'wordWrap'  => true,
            'SMTPHost'  => 'mailhub.eait.uq.edu.au',
            'SMTPPort'  => 25,
        ];
        $emailer->initialize($emailerConfig);
        $emailer->setTo($session->get('emailToChangePassword'));
        $emailer->setFrom('s4641616@student.uq.edu.au');
        $emailer->setSubject('Attent - Reset Password');
        $emailer->setMessage("
        Please click or enter the below link to change your password:
        "
        . base_url('login/forgot_password/' . $session->get('emailToChangePassword') . '/' . $session->get('resetToken')) .
        "
        Note: this link will expire at the end of the browser session."
        );
        if (! $emailer->send()) {
            echo "<p>Attempt to send email failed.</p>";
        }

        echo "<p>Email with password reset link has been sent</p>";
        return view('forgot_password');
    }

    public function resetPassword($email, $token) {
        $session = session();
        if ($token === $session->get('resetToken')) {
            return view('change_forgotten_password');
        } else {
            return redirect()->to(base_url('forgot_password'));
        }
    }

    public function changeForgottenPassword() {
        $password = $this->request->getPost('new_pw');

        // validation
        $rules = [
            'new_pw'   => 'required|min_length[8]',
            'confirm_pw' => 'required|matches[new_pw]',
        ];
        if (! $this->validate($rules)) {
            echo "<p>Invalid</p>";
            return view('change_forgotten_password');
        }

        // update
        $session = session();
        $userModel = model('UserModel');
        $userModel->changePassword($session->get('emailToChangePassword'), $password);

        $session->remove('resetToken');
        $session->remove('emailToChangePassword');

        echo "<p>Password has been changed</p>";
        return view('change_forgotten_password');
    }
}