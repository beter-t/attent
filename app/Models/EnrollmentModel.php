<?php
namespace App\Models;

use CodeIgniter\Model;

class EnrollmentModel extends Model
{
    protected $table = 'Enrollment';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'subject_id'];
    protected $validationRules = [
        'user_id'    => 'required|numeric',
        'subject_id' => 'required|numeric',
    ];

    public function addEnrollment($userID, $subjectID) {
        return $this->insert(['user_id' => $userID, 'subject_id' => $subjectID], false);
    }

    public function getUserEnrollments($userID) {
        return $this->select('id, subject_id')->where('user_id', $userID)->findAll();
    }

    public function checkEnrollment($userID, $subjectID) {
        return $this->where(['user_id' => $userID, 'subject_id' => $subjectID])->first() ? true : false;
    }
}