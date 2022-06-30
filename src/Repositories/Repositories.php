<?php

declare(strict_types=1);

namespace Xgbnl\Business\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

abstract class Repositories extends CacheGenerator
{
    // Store models
    private array $models = [];

    // Search filter fields
    protected array $searchFilters = [];

    // Load model with relation fields
    protected array $relations = [];

    // Except paginator fields
    protected array $excepts = [
        'pageNum',
        'pageSize',
    ];

    /**
     * Call sub class transform
     * @param Model|null $model
     * @return array
     */
    final public function transform(?Model $model): array
    {
        if (is_null($model)) {
            return [];
        }

        return $this->doTransform($model);
    }

    /**
     * Query data by id or other field.
     * @param mixed $value
     * @param string $by
     * @param array $with
     * @param array $columns
     * @param bool $array
     * @return array|Model|null
     */
    final public function find(mixed $value, string $by = 'id', array $with = [], array $columns = ['*'], bool $array = true): array|Model|null
    {
        $model = $this->checkWith($with)->where($by, $value)->first($columns);

        if ($array) {
            return $this->transform($model);
        }

        return $model;
    }

    /**
     * 获取所有数据
     * @param array $with
     * @param int $count
     * @return array
     */
    final public function fetchAll(array $with = [], int $count = 100,): array
    {
        $this->checkWith($with)->chunkById($count, function ($models) {
            $models->map(function ($model) {
                $this->models[] = $this->transform($model);
            });
        });

        return $this->models;
    }

    /**
     * 过滤查询
     * @param array $values
     * @param array $with
     * @return array
     */
    final public function filterSearch(array $values, array $with = []): array
    {
        $values = array_filter($values, fn($val) => !empty($val));

        $values = array_filter($values, fn($key) => !in_array($key, $this->excepts), ARRAY_FILTER_USE_KEY);

        if (empty($values)) {
            return $this->fetchAll();
        }

        $query = $this->checkWith($with);

        foreach ($values as $column => $value) {
            // if operate query
            if (array_key_exists($column, $this->searchFilters)) {
                $query = match ($operate = array_shift($this->searchFilters[$column])) {
                    'like' => $query->where($column, $operate, str_replace('?', $value, array_shift($this->searchFilters[$column]))),
                    'date' => $query->whereDate($column, '>=', $value)->orWhereDate($column, '<=', $value),
                };

            } elseif (in_array($column, $this->searchFilters)) {
                $query = $query->where($column, $value);
            }
        }

        $query->get()->map(function ($model) {
            $this->models[] = $this->transform($model);
        });

        return $this->models;
    }

    private function checkWith(array $with): Builder
    {
        $relation = !empty($with) ? $with : $this->relations;

        if (!empty($relation)) {

            $query = clone $this->query;

            return $query->with($relation);
        }

        return clone $this->query;
    }

    // abstract methods
    abstract protected function doTransform(Model $model): array;
}
