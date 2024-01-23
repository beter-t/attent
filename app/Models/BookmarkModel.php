<?php
namespace App\Models;

use CodeIgniter\Model;

class BookmarkModel extends Model
{
    protected $table = 'Bookmarks';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'post_id'];

    public function toggle($email, $postID) {
        $userModel = model('UserModel');
        $user = $userModel->getUser($email);
        $bookmark = $this->where('user_id', $user['id'])->where('post_id', $postID)->first();
        if ($bookmark) {
            $this->delete($bookmark['id']);
        } else {
            $this->insert(['user_id' => $user['id'], 'post_id' => $postID]);
        }
    }

    public function hasBookmark($email, $postID) {
        $userModel = model('UserModel');
        $user = $userModel->getUser($email);
        $bookmark = $this->where('user_id', $user['id'])->where('post_id', $postID)->first();
        if ($bookmark) {
            return true;
        } else {
            return false;
        }
    }

    public function getBookmarks($email) {
        $userModel = model('UserModel');
        $user = $userModel->getUser($email);
        return $this->where('user_id', $user['id'])->findAll();
    }

    public function getBookmarkers($postID) {
        return $this->select('user_id')->where('post_id', $postID)->findAll();
    }
}