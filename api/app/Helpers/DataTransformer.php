<?php


namespace App\Helpers;


class DataTransformer
{
    public static function transform(string $model, array $item):array
    {
        $model = new $model;
        $fields = $model->getFillable();
        $transform = [];
        foreach ($fields as $key => $value) {
            if (is_string($value) && array_key_exists($value, $item)) {
                $transform[$value] = $item[$value];
                continue;
            }
        }
        return $transform;
    }
}
