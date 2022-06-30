<?php

namespace Xgbnl\Business\Cache;

use Redis;
use RedisException;
use Xgbnl\Business\Repositories\Repositories;
use Xgbnl\Business\Utils\Fail;

abstract class Cacheable
{
    protected ?Repositories $repositories = null;

    protected ?Redis $redis = null;

    protected array $caches = [];

    // Cacheable Fields
    protected array $cacheFields = [];

    final public function __construct(Repositories $repository = null)
    {
        $this->repositories = $repository;

        $this->connectRedis(env('REPOSITORY_CACHE', 'default'));
    }

    private function connectRedis(string $connect): void
    {
        try {
            $this->redis = \Illuminate\Support\Facades\Redis::connection($connect)->client();
        } catch (RedisException $e) {
            Fail::throwFailException(message: 'redis连接错误:[ ' . $e->getMessage() . ' ],请检查并确认您的配置');
        }
    }

    /**
     * 通过Key获取缓存
     * @param string $key
     * @return array
     */
    final public function fetchByKey(string $key): array
    {
        if (!$this->redis->exists($key)) {
            $this->store($key);

            return $this->caches;
        }

        try {
            return json_decode($this->redis->get($key), true);
        } catch (RedisException $e) {
            Fail::throwFailException(message: '从redis中获取缓存出错: [ ' . $e->getMessage() . ' ]', throwable: $e);
        }
    }

    private function store(string $key): void
    {
        try {
            $this->configure();

            if (!empty($this->caches)) {
                $this->redis->set($key, json_encode($this->caches, JSON_UNESCAPED_UNICODE));
            }
        } catch (RedisException $e) {
            Fail::throwFailException(message: '数据写入redis时出错: [ ' . $e->getMessage() . ' ]', throwable: $e);
        }
    }

    /**
     * 清除缓存
     * @return void
     */
    final public function forget(): void
    {
        if (empty($this->cacheFields) && $this->redis->exists($this->cacheFields)) {
            $this->redis->del($this->cacheFields);
        }
    }

    /**
     * 生成树结构数组
     * @param array $list
     * @param string $id
     * @param string $pid
     * @param string $son
     * @return array
     */
    final protected function tree(array $list, string $id = 'id', string $pid = 'pid', string $son = 'children'): array
    {
        list($tree, $map) = [[], []];
        foreach ($list as $item) {
            $map[$item[$id]] = $item;
        }

        foreach ($list as $item) {
            if (isset($item[$pid]) && isset($map[$item[$pid]])) {
                $map[$item[$pid]][$son][] = &$map[$item[$id]];
            } else {
                $tree[] = &$map[$item[$id]];
            }
        }

        unset($map);
        return $tree;
    }

    /**
     * 静态清除缓存
     * @return void
     */
    static final public function clearCaches(): void
    {
        $inst = new static();
        $inst->forget();
    }

    /**
     * 设置缓存数据源
     * @return void
     */
    abstract protected function configure(): void;
}
