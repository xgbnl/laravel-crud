<?php

namespace Xgbnl\Business\Repository;

use Illuminate\Contracts\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;
use Xgbnl\Business\Cache\Cache;
use Xgbnl\Business\Contacts\Magic;
use Xgbnl\Business\Enum\GeneratorEnum;
use Xgbnl\Business\Fail;
use Xgbnl\Business\Traits\Generator;

/**
 * @property-read Cache|null $cache
 * @property-read QueryBuilder $rawQuery
 */
abstract class BaseRepository implements Magic
{
    use Generator;

    private ?string $cacheModel = null;

    public function magicGet(string $propertyName): Cache
    {
        return match ($propertyName) {
            'cache'    => $this->makeModel(
                parentClass: Cache::class,
                callMethod : 'getCacheModel',
                parameters : ['repository' => $this],
            ),
            'rawQuery' => $this->table
                ? DB::table($this->table)
                : Fail::throwFailException('获取数据表:[ ' . $this->table . ' ]错误'),
        };
    }

    private function getCacheModel(): string
    {
        if (!is_null($this->cacheModel)) {
            return $this->cacheModel;
        }

        $clazz = $this->getClazz();

        $class = array_pop($clazz);

        $ns = implode('\\', $clazz);
        $ns = $ns . '\\Caches\\';

        $class = $this->strEndWith($class, GeneratorEnum::REPOSITORY);

        $class = $ns . $class . 'Cache';

        $this->resolveClassFail($class, '缺少仓库缓存模型 [ ' . $class . ' ]');

        $this->cacheModel = $class;

        return $this->cacheModel;
    }
}
