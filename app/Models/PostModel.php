<?php
namespace App\Models;

use CodeIgniter\Model;

class PostModel extends Model
{
    protected $table = 'Post';
    protected $primaryKey = 'id';
    protected $allowedFields = ['title', 'body', 'post_type', 'public', 'subject_id', 'category_id', 'anonymous', 'user_id', 'views'];

    public function addPost($post) {
        return $this->insert($post, false);
    }

    public function inSubject($postID, $subjectID) {
        $post = $this->find($postID);
        if ($post && $post['subject_id'] == $subjectID) {
            return true;
        } else {
            return false;
        }
    }
}