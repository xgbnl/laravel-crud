<?php

namespace Xgbnl\Business\Traits;

use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Xgbnl\Business\Cache\Cacheable;
use Xgbnl\Business\Enum\GeneratorEnum;
use Xgbnl\Business\Fail;

/**
 * @property-read Model $model
 * @property-read string|null $table
 * @property-read EloquentBuilder $query
 *
 */
trait Generator
{
    private ?string $model = null;
    private ?string $table = null;

    final public function __get(string $name)
    {
        return match ($name) {
            'table' => $this->table ?? $this->makeModel()->getTable(),
            'model' => $this->makeModel(),
            'query' => $this->makeModel(instance: false)::query(),
            default => $this->magicGet($name),
        };
    }

    private function getModel(): string
    {
        if (!is_null($this->model)) {
            return $this->model;
        }

        $baseName = $this->strEndWith(
            last($this->getClazz()),
            [ucwords(GeneratorEnum::SERVICE), ucwords(GeneratorEnum::REPOSITORY)]
        );

        $clazz = 'App\\Models\\' . $baseName;

        $this->resolveClassFail($clazz, '缺少模型 [ ' . $baseName . ' ]');

        return $this->model = $clazz;
    }

    private function makeModel(string $parentClass = Model::class, string $callMethod = 'getModel', bool $instance = true, array $parameters = []): Cacheable|Model|string
    {
        if (!method_exists($this, $callMethod)) {
            Fail::throwFailException(message: '调用的方法[ ' . $callMethod . ' ]不存在');
        }

        $class = $this->{$callMethod}();

        $modelType = match (true) {
            str_ends_with($parentClass, 'Model') => '模型',
            str_ends_with($parentClass, 'Cacheable') => '仓库缓存',
        };

        if (!is_subclass_of($class, $parentClass)) {

            $msg = $modelType . '文件 [ ' . $class . ' ]错误,必须继承 [ ' . $parentClass . ' ]';

            Log::error($msg);
            Fail::throwFailException($msg);
        }

        if (!$instance) {
            return $class;
        }

        try {
            if (!empty($parameters)) {
                return app($class, $parameters);
            }

            return app($class);

        } catch (BindingResolutionException $exception) {

            Fail::throwFailException(message: $modelType . '文件实例化时错误:', throwable: $exception->getTrace());
        } catch (Exception $e) {

            Fail::throwFailException($e->getMessage());
        }
    }

    final static public function strEndWith(string $haystack, string|array $needle): string
    {
        if (is_string($needle)) {
            return str_ends_with($haystack, $needle) ? substr($haystack, 0, -strlen($needle)) : $haystack;
        }

        foreach ($needle as $need) {
            if (str_ends_with($haystack, $need)) {
                return substr($haystack, 0, -strlen($need));
            }
        }

        return $haystack;
    }

    private function getClazz(): array
    {
        return explode('\\', get_called_class());
    }

    private function resolveClassFail(string $class, string $message): void
    {
        if (!class_exists($class)) {
            Fail::throwFailException(message: $message);
        }
    }
}
