<?php

namespace App\Repository\Eloquent;

use App\Helpers\SearchSortPaginateHelper;
use App\Models\UserAsAModel;
use App\Repository\UserRepositoryInterface;
use Illuminate\Support\Collection;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    /**
     * UserRepository constructor.
     *
     * @param UserAsAModel $model
     */
    public function __construct(UserAsAModel $model)
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
