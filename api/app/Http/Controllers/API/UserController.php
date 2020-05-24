<?php

namespace App\Http\Controllers\API;

use App\Helpers\ExternalApiHelper;
use App\Http\Controllers\Controller;
use App\Models\UserAsAModel;
use App\Repository\UserRepositoryInterface;
use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use \Illuminate\Support\Facades\Response;

class UserController extends Controller
{
    protected $model = 'App\Models\UserAsAModel';

    private $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Collection
     */
    public function index(): Collection
    {
        return $this->userRepository->all();
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Collection
     */
    public function show($id): Collection
    {
        return $this->userRepository->find($id);
    }
}
