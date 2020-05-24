<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Repository\PostRepositoryInterface;
use Illuminate\Support\Collection;

class PostController extends Controller
{
    protected $model = 'App\Models\Post';

    private $postRepository;

    public function __construct(PostRepositoryInterface $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Collection
     */
    public function index($users = null): Collection
    {
        if ($users) {
            return $this->getPostsByUserId($users);
        }

        return $this->postRepository->all();
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Collection
     */
    public function show($id): Collection
    {
        return $this->postRepository->find($id);
    }

    /**
     * Gets posts by userId
     *
     * @param $userId
     */
    private function getPostsByUserId(int $userId) : Collection
    {
        return $this->postRepository->getWhere('userId', $userId);
    }
}
