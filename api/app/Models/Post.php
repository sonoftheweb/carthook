<?php

namespace App\Models;

use App\Http\Resources\Post\PostResource;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $table = 'posts';

    protected $fillable = [
        'id',
        'userId',
        'title',
        'body',
    ];

    public $queryable = [
        'id',
        'userId',
    ];

    public $dataResource = [
        'resource' => PostResource::class
    ];

    public function comments()
    {
        return $this->hasMany(Comment::class, 'postId', 'id');
    }

    public function user()
    {
        return $this->belongsTo(UserAsAModel::class, 'userId', 'id');
    }
}
