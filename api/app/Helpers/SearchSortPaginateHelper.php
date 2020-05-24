<?php


namespace App\Helpers;


use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class SearchSortPaginateHelper
{
    public static function searchSortAndPaginate($query)
    {
        try {
            $parameters = request()->except(['_limit', '_page', '_with']);

            $model = $query->getModel();
            $resourceClass = $model->dataResource['resource'];
            $attributesToQuery = array_flip($model->queryable);
            $parameters = array_flip(array_intersect_key($attributesToQuery, $parameters));

            if (!empty($parameters))
                $query = self::whereClauseBuilder($query, $parameters);

            if (request()->has('_search'))
                $query = self::searchBuilder($query, request()->_search);

            $_limit = (request()->has('_limit')) ? request()->_limit : env('DEFAULT_PAGINATION');
            $_page = (request()->has('_page')) ? request()->_page : 1;

            return $resourceClass::collection($query->paginate($_limit, ['*'], 'page', $_page));
        } catch(QueryException $e){
            Log::error($e->getMessage().' in ' . $e->getFile() . ' on line ' . $e->getLine());
            abort(401, 'This query cannot be completed.'); // abort with 401 so data about error is not shared
        }
    }

    public static function whereClauseBuilder($query, $params=null)
    {
        if (!empty($params)) {
            foreach ($params as $key => $param) {
                $query = $query->where($param, request()->get($param));
            }
        }

        return $query;
    }

    public static function searchBuilder($query, $value)
    {
        switch ($query->getModel()->getTable()) {
            case 'users':
                $matched = 'name, email, username';
                break;
            case 'posts':
                $matched = 'title';
                break;
            default:
                $matched = null;
        }

        if ($matched)
            return $query->whereRaw("MATCH(". $matched .") AGAINST('" . $value . "')");

        return $query;
    }
}
