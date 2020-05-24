<?php

namespace App\Http\Resources\User;

use App\Http\Resources\Post\PostResource;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request):array
    {
        $returns = [
            'id' => $this->id,
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'phone' => $this->phone,
            'website' => $this->website,
        ];

        if (in_array('posts', explode(',', $request->_with))) {
            $returns['posts'] = PostResource::collection($this->resource->posts);
        }

        return $returns;
    }
}
