<?php
namespace App\Models;

use CodeIgniter\Model;

class PostImageModel extends Model
{
    protected $table = 'Post_Images';
    protected $primaryKey = 'id';
    protected $allowedFields = ['post_id', 'filename'];

    public function addImagePath($postID, $imgPath) {
        return $this->insert(['post_id' => $postID, 'filename' => $imgPath], false);
    }

    public function getPostImages($postID) {
        return $this->where('post_id', $postID)->findAll();
    }
}