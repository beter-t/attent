<?php
namespace App\Models;

use CodeIgniter\Model;

class PostLikesModel extends Model
{
    protected $table = 'Post_Likes';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'post_id'];

    public function toggle($email, $postID) {
        $userModel = model('UserModel');
        $user = $userModel->getUser($email);
        $like = $this->where('user_id', $user['id'])->where('post_id', $postID)->first();
        if ($like) {
            $this->delete($like['id']);
        } else {
            $this->insert(['user_id' => $user['id'], 'post_id' => $postID]);
        }
    }

    public function hasLiked($email, $postID) {
        $userModel = model('UserModel');
        $user = $userModel->getUser($email);
        $like = $this->where('user_id', $user['id'])->where('post_id', $postID)->first();
        if ($like) {
            return true;
        } else {
            return false;
        }
    }

    public function totalLikes($postID) {
        $result = $this->where('post_id', $postID)->countAllResults();
        return $result;
    }
}