<?php


namespace App\Repository;


use Illuminate\Support\Collection;

interface PostRepositoryInterface
{
    public function all(): Collection;
}
