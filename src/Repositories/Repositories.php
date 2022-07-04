<?php

declare(strict_types=1);

namespace Xgbnl\Business\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Xgbnl\Business\Attributes\Business;
use Xgbnl\Business\Traits\ReflectionParse;
use Xgbnl\Business\Utils\MagicMethods;

/**
 * @method mixed endpoint(mixed $needle, string $domain, bool $replace = false) 为图像添加或移除域名
 */
#[Business(MagicMethods::class)]
abstract class Repositories extends AbstractRepositories
{
    use ReflectionParse;

    // 如果前端分页参数是数组成员，则忽略
    protected array $excepts = [
        'pageNum',
        'pageSize',
    ];

    private function transform(?Model $model): array
    {
        return is_null($model) ? [] : $this->doTransform($model);
    }

    /**
     * 通过给定字段查找模型
     * @param mixed $value
     * @param string $by
     * @param array $with
     * @param bool $array true返回数组，false返回模型对象
     * @return array|Model|null
     */
    final public function find(mixed $value, string $by = 'id', array $with = [], bool $array = true): array|Model|null
    {
        $model = $this->checkWith($with)->where($by, $value)->first();

        return $array ? $this->transform($model) : $model;
    }

    /**
     * 获取所有模型
     * @param array $with
     * @param int $count
     * @return array
     */
    final public function fetchAll(array $with = [], int $count = 100): array
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

        return !empty($relation) ? clone $this->query->with($relation) : clone $this->query;
    }
}
