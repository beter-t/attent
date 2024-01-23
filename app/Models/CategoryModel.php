<?php
namespace App\Models;

use CodeIgniter\Model;

class CategoryModel extends Model
{
    protected $table = 'Category';
    protected $primaryKey = 'id';
    protected $allowedFields = ['subject_id', 'name'];
    protected $validationRules = [
        'name' => 'required|alpha_numeric',
    ];

    public function addCategory($subject_id, $name) {
        return $this->insert(['subject_id' => $subject_id, 'name' => $name], false);
    }

    public function checkExists($name) {
        return $this->where('name', $name)->first() ? true : false;
    }
}