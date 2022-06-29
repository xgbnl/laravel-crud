<?php

declare(strict_types=1);

namespace Xgbnl\Business\Service;

use Exception;
use Throwable;
use LogicException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Xgbnl\Business\Contacts\Observer;
use Xgbnl\Business\Fail;
use Xgbnl\Business\Traits\Generator;

abstract class Service
{
    use Generator;

    protected ?Observer $observer = null;

    final public function __construct()
    {
        $this->registerObserver();
    }

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

            try {
                DB::beginTransaction();

                $model = $this->query->updateOrCreate([$by => $byValue], $data);

                DB::commit();

                $this->notifyObserver($model, 'updated');

                return $model;
            } catch (Throwable $e) {

                DB::rollBack();

                $msg = '更新数据错误 [ ' . $e->getMessage() . ' ]';
                Log::error($msg);
                Fail::throwFailException(message: $msg, throwable: $e);
            }
        }

        try {
            DB::beginTransaction();

            $model = $this->query->create($data);

            DB::commit();

            $this->notifyObserver($model, 'created');

            return $model;
        } catch (Throwable $e) {
            DB::rollBack();

            $msg = '创建数据错误 [ ' . $e->getMessage() . ' ]';
            Log::error();
            Fail::throwFailException(message: $msg, throwable: $e);
        }
    }

    /**
     * 批量删除模型或删除单个模型
     * 批量删除无法触发观察者
     * @param int|array $value
     * @param string $by
     * @return bool
     */
    final public function destroy(int|array $value, string $by = 'id'): bool
    {
        if (is_array($value)) {
            $this->query->whereIn($by, $value)->delete();
        }

        $model = $this->query->where($by, $value)->first();

        try {
            $model->delete();

        } catch (LogicException $e) {

            $error = '模型(' . class_basename($this) . ')上没有定义主键或数据不存在: [ ' . $e->getMessage() . ' ]';

            Log::error($error);

            Fail::throwFailException(message: $error);
        } catch (Exception $e) {

            $error = class_basename($this) . '::destroy' . '删除数据失败:[ ' . $e->getMessage() . ' ]';

            Log::error($error);

            Fail::throwFailException(message: $error);
        }

        $this->notifyObserver($model, 'deleted');

        return true;
    }

    /**
     * 通知观察者
     * @param Model $model
     * @param string $method
     * @return void
     */
    private function notifyObserver(Model $model, string $method): void
    {
        if (!is_null($this->observer)) {
            $this->observer->{$method}($model);
        }
    }

    /**
     * 实例化观察者
     * @param string $observer
     * @return void
     */
    final protected function registerObserverInstance(string $observer): void
    {
        if (!is_subclass_of($observer, Observer::class)) {
            self::throwFailException('模型(' . $observer . ')必须实现:[ ' . Observer::class . ' ]的方法');
        }

        $this->observer = app($observer);
    }

    /**
     * 注册观察者
     * @return void
     */
    abstract protected function registerObserver(): void;
}
