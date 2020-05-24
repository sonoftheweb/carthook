<?php


namespace App\Repository\Eloquent;


use App\Helpers\SearchSortPaginateHelper;
use App\Models\Comment;
use App\Repository\CommentRepositoryInterface;
use Illuminate\Support\Collection;

class CommentRepository extends BaseRepository implements CommentRepositoryInterface
{
    /**
     * PostRepository constructor.
     *
     * @param Comment $model
     */
    public function __construct(Comment $model)
    {
        parent::__construct($model);
    }

    public function all(): Collection
    {
        $data = $this->model::query();

        $data = SearchSortPaginateHelper::searchSortAndPaginate($data);

        // if there are no data in db based on the query,
        // maybe we can attempt to query JsonPlaceholder?

        $data = collect($data->items());

        return $data;
    }
}
