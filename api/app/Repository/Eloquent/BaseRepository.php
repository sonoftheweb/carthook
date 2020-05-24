<?php

namespace App\Repository\Eloquent;

use App\Helpers\SearchSortPaginateHelper;
use App\Repository\EloquentRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class BaseRepository implements EloquentRepositoryInterface
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * BaseRepository constructor.
     *
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * @param array $attributes
     *
     * @return Model
     */
    public function create(array $attributes): Model
    {
        return $this->model->create($attributes);
    }

    /**
     * @param $id
     * @param array $relations
     * @return Collection|null
     */
    public function find($id, $relations = []): ?Collection
    {
        $data = $this->model->find($id);

        if (!$data)
            return collect([]);

        if (!empty($relations))
            $data->load($relations);

        $resourceClass = $data->getModel()->dataResource['resource'];
        $data = collect(new $resourceClass($data));

        return $data;
    }

    /**
     * Gets the collection with a where clause
     *
     * @param string $column
     * @param string $data
     * @return Collection|null
     */
    public function getWhere(string $column, string $data): ?Collection
    {
        $data = $this->model::query()->where($column, $data);
        $data = SearchSortPaginateHelper::searchSortAndPaginate($data);

        $resourceClass = $this->model::query()->getModel()->dataResource['resource'];
        $data = collect($resourceClass::collection($data));

        return $data;
    }
}
