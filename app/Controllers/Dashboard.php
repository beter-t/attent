<?php

namespace App\Controllers;

use App\Models\UserModel;

class Dashboard extends BaseController
{
    public $session;

    public function index() {
        $session = session();
        $userModel = model('UserModel');
        $enrollModel = model('EnrollmentModel');
        $subjectModel = model('SubjectModel');
        
        // Check login
        $email_auto = $session->get('email') ?: get_cookie('email');
        $hashed_pw_auto = $session->get('hashed_pw') ?: get_cookie('hashed_pw');
        if (!$email_auto || $hashed_pw_auto !== $userModel->getHashedPassword($email_auto)) {
            return redirect()->to(base_url('login'));
        }
        
        // Header nav bar
        $userData = $userModel->getUser($session->get('email'));
        echo view('logged_in_header', $userData);

        
        
        // Get user's enrolled subjects
        $enrollmentsRaw = $enrollModel->getUserEnrollments($userData['id']);
        $enrollments = [];
        foreach ($enrollmentsRaw as $enrollment) {
            $enrollments[$enrollment['id']] = [
                'id'   => $enrollment['subject_id'], 
                'name' => $subjectModel->find($enrollment['subject_id'])['name'],
            ];
        }
        return view('dashboard', ['enrollments' => $enrollments]);
    }

    public function addSubject() {
        $session = session();
        $userModel = model('UserModel');
        $enrollModel = model('EnrollmentModel');
        $subjectModel = model('SubjectModel');

        // Enrol/ Create button click
        $userData = $userModel->getUser($session->get('email'));
        $subjectName = $this->request->getPost('subject');

        // instructor creates subjects, student enrols
        if ($userData['user_type'] === 'instructor') {
            if ($subjectModel->addSubject($subjectName)) {
                $subjectID = $subjectModel->getInsertID();
                $enrollModel->addEnrollment($userData['id'], $subjectID);

                $categoryModel = model('CategoryModel');
                $categoryModel->addCategory($subjectID, 'General'); // generic category for all subjects
                echo 'Subject added successfully';
            } else {
                echo 'Failed to add subject';
            }
        } else {
            $subjectData = $subjectModel->getSubject($subjectName);
            if (! $subjectData) {
                echo 'Subject does not exist';
            } else if ($enrollModel->checkEnrollment($userData['id'], $subjectData['id'])) {
                echo 'Already enrolled';
            } else if ($enrollModel->addEnrollment($userData['id'], $subjectData['id'])) {
                echo 'Successfully enrolled';
            } else {
                echo 'Failed to enrol';
            }
        }

        return redirect()->to(base_url('dashboard'));
    }
}