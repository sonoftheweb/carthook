<?php

namespace App\Http\Resources\Post;

use App\Http\Resources\Comment\CommentResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request):array
    {
        $returns = [
            'id' => $this->id,
            'userId' => $this->userId,
            'title' => $this->title,
            'body' => $this->body,
        ];

        if (in_array('comments', explode(',', $request->_with))) {
            $returns['comments'] = CommentResource::collection($this->comments);
        }

        return $returns;
    }
}
