<?php
namespace App\Models;

use CodeIgniter\Model;

class CommentModel extends Model
{
    protected $table = 'Comment';
    protected $primaryKey = 'id';
    protected $allowedFields = ['body', 'post_id', 'user_id', 'parent_id'];

    public function addComment($data) {
        /*$user = $this->where('email', $email)->first();
        $data = [
            'user_id' => $user['id'],
            'post_id' => $postID,
            'parent_id' => $parentID,
            'body' => $body,
        ];*/
        return $this->insert($data, false);
    }

    public function getPostComments($postID) {
        $userModel = model('UserModel');
        $query = $this->where('post_id', $postID)->findAll();
        $result = [];
        foreach ($query as $comment) {
            $user = $userModel->find($comment['user_id']);
            $comment['user_fullname'] = $user['first_name'] . ' ' . $user['last_name'];
            $result[] = $comment;
        }
        return $result;
    }
}