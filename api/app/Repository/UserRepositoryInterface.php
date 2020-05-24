<?php


namespace App\Repository;

use Illuminate\Support\Collection;

interface UserRepositoryInterface
{
    public function all(): Collection;
}
