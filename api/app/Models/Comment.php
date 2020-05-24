<?php

namespace App\Models;

use App\Http\Resources\Comment\CommentResource;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $table = 'comments';

    protected $fillable = [
        'postId',
        'id',
        'name',
        'email',
        'body',
    ];

    public $queryable = [
        'postId',
        'email',
    ];

    public $dataResource = [
        'resource' => CommentResource::class
    ];

    public function post()
    {
        return $this->belongsTo(Post::class, 'postId', 'id');
    }
}
