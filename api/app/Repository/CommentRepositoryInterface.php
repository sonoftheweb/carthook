<?php


namespace App\Repository;


use Illuminate\Support\Collection;

interface CommentRepositoryInterface
{
    public function all(): Collection;
}
