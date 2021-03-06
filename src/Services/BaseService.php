<?php

declare(strict_types=1);

namespace Xgbnl\Business\Services;

use Exception;
use LogicException;
use ReflectionClass;
use Throwable;
use Xgbnl\Business\Traits\BuilderGenerator;
use Xgbnl\Business\Utils\Fail;
use Illuminate\Database\Eloquent\{Model, ModelNotFoundException};
use Illuminate\Support\Facades\{DB, Log};

abstract class BaseService extends Observable
{
    use BuilderGenerator;

    /**
     * 创建模型或更新模型
     * @param array $data 需要更新的数据
     * @param string $by 默认为根据id更新
     * @return Model
     */
    final public function createOrUpdate(array $data, string $by = 'id'): Model
    {
        if ($byValue = ($data[$by] ?? null)) {
            if ($by === 'id') {
                unset($data[$by]);
            }
            return $this->updated([$by => $byValue], $data);
        }

        return $this->created($data);
    }

    private function updated(array $attributes, array $data): Model
    {
        try {
            DB::beginTransaction();

            $this->modelClass = $this->query->updateOrCreate($attributes, $data);

            DB::commit();

            $this->triggerMethod(__METHOD__);

            $this->notify();

            return $this->modelClass;
        } catch (Throwable $e) {

            DB::rollBack();

            $msg = '更新数据错误 [ ' . $e->getMessage() . ' ]';
            Log::error($msg);
            Fail::throwFailException(message: $msg, throwable: $e);
        }
    }

    private function created(array $data): Model
    {
        try {
            DB::beginTransaction();

            $this->modelClass = $this->query->create($data);

            DB::commit();

            $this->triggerMethod(__METHOD__);
        } catch (Throwable $e) {
            DB::rollBack();

            $msg = '创建数据错误 [ ' . $e->getMessage() . ' ]';
            Log::error($msg);
            Fail::throwFailException(message: $msg, throwable: $e);
        }

        $this->notify();
        return $this->modelClass;
    }

    /**
     * 批量删除模型或删除单个模型
     * 批量删除无法触发观察者
     * @param int|array $value
     * @param string $by
     * @return int|bool
     */
    final public function destroy(int|array $value, string $by = 'id'): int|bool
    {
        return is_array($value) ? $this->batchDeleted($value, $by) : $this->deleted($value, $by);
    }

    private function batchDeleted(array $values, string $by): int
    {
        $count = 0;

        foreach ($this->query->whereIn($by, $values)->get() as $model) {
            if ($model->delete()) {
                $count++;
            }
        }

        return $count;
    }

    private function deleted(int $value, string $by): bool
    {
        $this->modelClass = $this->query->where($by, $value)->first();

        try {
            $this->modelClass->delete();

            $this->triggerMethod(__METHOD__);

        } catch (LogicException $e) {

            $error = '模型(' . class_basename($this) . ')上没有定义主键或数据不存在: [ ' . $e->getMessage() . ' ]';

            Log::error($error);

            Fail::throwFailException(message: $error, throwable: $e);
        } catch (Exception $e) {

            $error = class_basename($this) . '::destroy' . '删除数据失败:[ ' . $e->getMessage() . ' ]';

            Log::error($error);

            Fail::throwFailException(message: $error, throwable: $e);
        }

        $this->notify();

        return true;
    }

    protected function registerObserver(): void
    {
        $this->observer = null;
    }

    /**
     * 获取模型批量赋值属性
     * @param string $property 默认为fillable
     * @return array
     */
    final public function getModelProperty(string $property = 'fillable'): array
    {
        $ref = new ReflectionClass($this->model);

        return $ref->hasProperty($property)
            ? $ref->getProperty($property)->getDefaultValue()
            : Fail::throwFailException('模型' . $this->modelName . '不存在属性:[' . $property . ']');
    }
}
