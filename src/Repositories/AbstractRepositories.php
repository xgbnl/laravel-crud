<?php

namespace Xgbnl\Business\Repositories;

use Xgbnl\Business\Utils\Fail;
use Illuminate\Support\Facades\DB;
use Xgbnl\Business\Contacts\Magic;
use Xgbnl\Business\Traits\BuilderGenerator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Database\Query\Builder as QueryBuilder;

/**
 * @property-read QueryBuilder $rawQuery
 */
abstract class AbstractRepositories implements Magic
{
    use BuilderGenerator;

    /**
     * 存储模型
     * @var array
     */
    protected array $models = [];

    /**
     * 过滤搜索关键字
     * @var array
     */
    protected array $searchFilters = [];

    /**
     * 需要加载的模型关系
     * @var array
     */
    protected array $relations = [];

    /**
     * 获取原生查询
     * @param string $propertyName
     * @return QueryBuilder
     */
    public function magicGet(string $propertyName): QueryBuilder
    {
        return match ($propertyName) {
            'rawQuery' => $this->table
                ? DB::table($this->table)
                : Fail::throwFailException('获取数据表:[ ' . $this->table . ' ]错误'),
        };
    }

    /**
     * 抽象转换层
     * @param Model $model
     * @return array
     */
    abstract protected function doTransform(Model $model): array;
}
