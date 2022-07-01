<?php

namespace Xgbnl\Business\Controllers;

use Illuminate\Http\JsonResponse;
use Xgbnl\Business\Paginator\Paginator;

/**
 * @method JsonResponse json(mixed $data = null, int $code = 200)
 * @method Paginator customPaginate(array $list = [], bool $isPaginate = true)
 * @method array filter(array $arrays)
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

        abort_if(empty($id), 422, '无效数据获取，参数ID不能为空');

        $model = $this->repository->find($id);

        if (!empty($merge)) {
            $model = array_merge($model, $merge);
        }

        return $this->json($model);
    }

    final protected function doStoreOrUpdate(array $extras = [], string $by = 'id', mixed $filterFields = null): JsonResponse
    {
        $validated = $this->validatedForm($extras);

        abort_if(empty($validated), 422, '插入数据不能为空');

        $validated = $this->filter($validated);

        if (is_string($filterFields) && isset($validated[$filterFields])) {
            unset($validated[$filterFields]);
        }

        if (is_array($filterFields)) {
            foreach ($filterFields as $field) {
                if (isset($validated[$field])) {
                    unset($validated[$field]);
                }
            }
        }

        $model = $this->service->createOrUpdate($validated, by: $by);

        return $this->json($model);
    }

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

        abort_if(empty($this->request->all()), 422, '无效的删除');

        $ids = $this->filter($this->request->all());

        $this->service->destroy($ids);

        return $this->json();
    }
}
