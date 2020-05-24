<?php

namespace App\Repository\Eloquent;

use App\Helpers\SearchSortPaginateHelper;
use App\Models\Post;
use App\Repository\PostRepositoryInterface;
use Illuminate\Support\Collection;

class PostRepository extends BaseRepository implements PostRepositoryInterface
{
    /**
     * PostRepository constructor.
     *
     * @param Post $model
     */
    public function __construct(Post $model)
    {
        parent::__construct($model);
    }

    /**
     * @return Collection
     */
    public function all(): Collection
    {
        $data = $this->model::query();

        $data = SearchSortPaginateHelper::searchSortAndPaginate($data);

        $data = collect($data->items());

        return $data;
    }
}
