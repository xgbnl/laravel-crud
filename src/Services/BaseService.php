<?php

declare(strict_types=1);

namespace Xgbnl\Business\Services;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use LogicException;
use Throwable;
use Xgbnl\Business\Traits\BuilderGenerator;
use Xgbnl\Business\Utils\Fail;

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

            try {
                DB::beginTransaction();

                $this->model = $this->query->updateOrCreate([$by => $byValue], $data);

                DB::commit();

                $this->trigger = 'updated';

                $this->notify();

                return $this->model;
            } catch (Throwable $e) {

                DB::rollBack();

                $msg = '更新数据错误 [ ' . $e->getMessage() . ' ]';
                Log::error($msg);
                Fail::throwFailException(message: $msg, throwable: $e);
            }
        }

        try {
            DB::beginTransaction();

            $this->model = $this->query->create($data);

            DB::commit();

            $this->trigger = 'created';
        } catch (Throwable $e) {
            DB::rollBack();

            $msg = '创建数据错误 [ ' . $e->getMessage() . ' ]';
            Log::error($msg);
            Fail::throwFailException(message: $msg, throwable: $e);
        }

        $this->notify();
        return $this->model;
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

        $this->model = $this->query->where($by, $value)->first();

        try {
            $this->model->delete();

            $this->trigger = 'deleted';

        } catch (LogicException $e) {

            $error = '模型(' . class_basename($this) . ')上没有定义主键或数据不存在: [ ' . $e->getMessage() . ' ]';

            Log::error($error);

            Fail::throwFailException(message: $error);
        } catch (Exception $e) {

            $error = class_basename($this) . '::destroy' . '删除数据失败:[ ' . $e->getMessage() . ' ]';

            Log::error($error);

            Fail::throwFailException(message: $error);
        }

        $this->notify();
        return true;
    }

    protected function registerObserver(): void
    {
        $this->observer = null;
    }
}
