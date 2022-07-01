<?php

declare(strict_types=1);

namespace Xgbnl\Business\Utils;

use Illuminate\Http\JsonResponse;
use Xgbnl\Business\Paginator\Paginator;

class MagicMethods
{
    static public function json(mixed $data = null, int $code = 200): JsonResponse
    {
        $r = ['msg' => null, 'code' => $code];

        if (is_string($data)) {
            $r['msg'] = $data;
        } elseif (!is_null($data)) {
            $r['data'] = $data;
        }

        return new JsonResponse($r);
    }

    static public function customPaginate(array $list = [], bool $isPaginate = true): Paginator
    {
        $pageNum  = (int)request()->get('pageNum', 1);
        $pageSize = (int)request()->get('pageSize', 10);

        $offset = ($pageNum * $pageSize) - $pageSize;

        $items = $isPaginate ? array_slice($list, $offset, $pageSize, true) : $list;

        $total = count($list);

        return new Paginator($items, $total, $pageSize, $pageNum, [
            'path'     => Paginator::resolveCurrentPath(),
            'pageName' => 'pageNum',
        ]);
    }

    static public function filter(array $arrays): array
    {
        return array_filter($arrays, fn($item) => !empty($item));
    }
}
