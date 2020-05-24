<?php

namespace App\Models;

use App\Http\Resources\User\UserResource;
use Illuminate\Database\Eloquent\Model;

/*
 * I put this model in place because I am not comfortable using the Laravel's authentication model in the way I am using it now.
 * Usually what I do is to break the application into modules and have each module work with the top layer application which
 * would act as the interface fof the User Auth and attributes only.
 * */
class UserAsAModel extends Model
{
    protected $table = 'users';

    protected $attributes = [
        'password' => 'password',
    ];

    protected $fillable = [
        'id',
        'name',
        'username',
        'email',
        'phone',
        'website', // ideally I'd store phone, website and username in an attribute table linked to this
        'password' // because we are using the same table as auth and I do not feel like it's wise to leave that empty
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public $queryable = [
        'name',
        'username',
        'email'
    ];

    public $dataResource = [
        'resource' => UserResource::class
    ];

    public function posts()
    {
        return $this->hasMany(Post::class, 'userId', 'id');
    }
}
