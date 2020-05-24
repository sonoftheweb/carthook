<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Repository\PostRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class UserPostController extends Controller
{
    private $userRepository;
    private $postRepository;

    public function __construct(UserRepositoryInterface $userRepository, PostRepositoryInterface $postRepository)
    {
        $this->userRepository = $userRepository;
        $this->postRepository = $postRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param int $users
     * @return Collection
     */
    public function index(int $users): Collection
    {
        return $this->postRepository->getWhere('userId', $users);
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
}
