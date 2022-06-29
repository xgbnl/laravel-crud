<?php

declare(strict_types=1);

namespace Xgbnl\Business\Traits;

use Xgbnl\Business\Paginator\Paginator;
use Illuminate\Http\JsonResponse;

trait ControllerHelpers
{
    final protected function json(mixed $data = null, int $code = 200): JsonResponse
    {
        $r = ['msg' => null, 'code' => $code];

        if (is_string($data)) {
            $r['msg'] = $data;
        } elseif (!is_null($data)) {
            $r['data'] = $data;
        }

        return new JsonResponse($r);
    }

    final protected function customPaginate(array $list = [], bool $isPaginate = true): Paginator
    {
        $pageNum  = (int)$this->request->get('pageNum', 1);
        $pageSize = (int)$this->request->get('pageSize', 10);

        $offset = ($pageNum * $pageSize) - $pageSize;

        $items = $isPaginate ? array_slice($list, $offset, $pageSize, true) : $list;

        $total = count($list);

        return new Paginator($items, $total, $pageSize, $pageNum, [
            'path'     => Paginator::resolveCurrentPath(),
            'pageName' => 'pageNum',
        ]);
    }
}
