<?php
namespace App\Models;

use CodeIgniter\Model;

class SubjectModel extends Model
{
    protected $table = 'Subject';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name'];
    protected $validationRules = [
        'name' => 'required|alpha_numeric',
    ];

    public function addSubject($name) {
        return $this->insert(['name' => $name], false);
    }

    public function getSubject($name) {
        return $this->where('name', $name)->first();
    }

}