<?php

namespace Xgbnl\Business\Controllers;

use Illuminate\Http\JsonResponse;
use Xgbnl\Business\Paginator\Paginator;
use  Illuminate\Database\Eloquent\Model;

/**
 * @method JsonResponse json(mixed $data = null, int $code = 200)
 * @method Paginator customPaginate(array $list = [], bool $isPaginate = true)
 * @method array filter(array $arrays)
 * @method array filterFields(array &$origin, array|string $fields)
 * @method void trigger(int $code, string $message)
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

    final protected function doStoreOrUpdate(array $extras = [], string $by = 'id', mixed $filterFields = null, bool $json = true): JsonResponse|Model
    {
        $validated = $this->validatedForm($extras);

        if (empty($validated)){
            $this->trigger(422,'插入数据不能为空');
        }

        $validated = $this->filter($validated);

        if (!is_null($filterFields)){
            $validated = $this->filterFields($validated, $filterFields);
        }

        $model = $this->service->createOrUpdate($validated, by: $by);

        return $json ? $this->json($model) : $model;
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

        if (empty($this->request->all())) {
            $this->trigger(422, '无效的删除');
        }

        $ids = $this->filter($this->request->all());

        $this->service->destroy($ids);

        return $this->json();
    }
}
