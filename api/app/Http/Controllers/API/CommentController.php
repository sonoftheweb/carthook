<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Repository\CommentRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class CommentController extends Controller
{
    private $commentRepository;

    public function __construct(CommentRepositoryInterface $commentRepository)
    {
        $this->commentRepository = $commentRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($posts = null):Collection
    {
        if ($posts)
            return $this->getCommentsByPostId($posts);

        return $this->commentRepository->all();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Gets comments by postId
     *
     * @param int $postId
     * @return Collection
     */
    private function getCommentsByPostId(int $postId) : Collection
    {
        return $this->commentRepository->getWhere('postId', $postId);
    }
}
