<?php

namespace Xgbnl\Business\Traits;

use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Xgbnl\Business\Enum\GeneratorEnum;
use Xgbnl\Business\Utils\Fail;
use Xgbnl\Business\Utils\Helper;

/**
 * @property-read Model $model
 * @property-read string|null $table
 * @property-read EloquentBuilder $query
 */
trait BuilderGenerator
{
    private ?string $modelName = null;
    private ?string $table     = null;

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
        if (!is_null($this->modelName)) {
            return $this->modelName;
        }

        $baseName = Helper::strEndWith(
            last($this->getClazz()),
            [ucwords(GeneratorEnum::SERVICE), ucwords(GeneratorEnum::REPOSITORY)]
        );

        $clazz = 'App\\Models\\' . $baseName;

        $this->resolveClassFail($clazz, '缺少模型 [ ' . $baseName . ' ]');

        return $this->modelName = $clazz;
    }

    private function makeModel(bool $instance = true): Model|string
    {
        $class = $this->getModel();

        if (!is_subclass_of($class, Model::class)) {

            $msg = '模型文件 [ ' . $class . ' ]错误,必须继承 [ ' . Model::class . ' ]';

            Log::error($msg);
            Fail::throwFailException($msg);
        }

        if (!$instance) {
            return $class;
        }

        try {

            return app($class);
        } catch (BindingResolutionException $exception) {

            Fail::throwFailException(message: '模型文件实例化时错误:', throwable: $exception->getTrace());
        } catch (Exception $e) {

            Fail::throwFailException($e->getMessage());
        }
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
