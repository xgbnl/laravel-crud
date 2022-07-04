<?php

declare(strict_types=1);

namespace Xgbnl\Business\Controllers;

use HttpRuntimeException;
use Illuminate\Http\JsonResponse;
use Xgbnl\Business\Paginator\Paginator;

/**
 * @method JsonResponse json(mixed $data = null, int $code = 200) 自定义Json返回
 * @method Paginator customPaginate(array $list = [], bool $isPaginate = true) 自定义分页
 * @method array filter(array $arrays) 过滤数组中值为null的键
 * @method array filterFields(array $origin, mixed $fields,bool $returnOrigin = true) 过滤指定的字段集合，$returnOrigin为true时返回过滤后的数组，反之返回一个Fields集合数组
 * @method void trigger(int $code, string $message) 触发一个Http异常
 */
abstract class CrudController extends AbstractController
{
    final protected function doIndex(): JsonResponse
    {
        $models = $this->repository->filterSearch($this->request->all());

        $pagesData = $this->customPaginate($models);

        return $this->json($pagesData);
    }

    final protected function doShow(?int $id = null, array $merge = []): JsonResponse
    {
        if (is_null($id)) {
            $id = $this->request->input('id');
        }

        if (empty($id)){
            $this->trigger(422,'ID不能为空');
        }

        $model = $this->repository->find($id);

        if (!empty($merge)) {
            $model = array_merge($model, $merge);
        }

        return $this->json($model);
    }

    final protected function doStoreOrUpdate(array $extras = [], string $by = 'id'): JsonResponse
    {
        $validated = $this->validatedForm($extras);

        if (empty($validated)){
            $this->trigger(422,'插入数据不能为空');
        }

        $validated = $this->filter($validated);

        $this->service->createOrUpdate($validated, by: $by);

        return $this->json();
    }

    /**
     * @throws HttpRuntimeException
     */
    final protected function doDestroy(?int $id = null): JsonResponse
    {
        if (!is_null($id)) {
            $this->service->destroy($id);
            return $this->json();
        }

        if ($id = $this->request->input('id')) {
            $this->service->destroy($id);

            return $this->json();
        }

        if (empty($this->request->all())) {
            $this->trigger(422, '无效的删除');
        }

        $ids = $this->filter($this->request->all());

        $this->service->destroy($ids);

        return $this->json();
    }
}
