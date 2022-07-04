<?php

declare(strict_types=1);

namespace Xgbnl\Business\Utils;

use HttpException;
use Illuminate\Http\JsonResponse;
use Xgbnl\Business\Paginator\Paginator;

class MagicMethods
{
    /**
     * Custom return json
     * @param mixed|null $data
     * @param int $code
     * @return JsonResponse
     */
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

    /**
     * Custom paginate.
     * @param array $list
     * @param bool $isPaginate
     * @return Paginator
     */
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

    /**
     * Custom filter array.
     * @param array $origin
     * @return array
     */
    static public function filter(array $origin): array
    {
        return array_filter($origin, fn($data) => !empty($data));
    }

    /**
     * Custom array field filtering.
     * @param array $origin
     * @param array|string $fields
     * @return array
     */
    static public function filterFields(array &$origin, array|string $fields): array
    {
        if (is_string($fields) && isset($origin[$fields])) {
            unset($origin[$fields]);
        }

        if (is_array($fields)) {
            foreach ($fields as $field) {
                if (isset($origin[$field])) {
                    unset($origin[$field]);
                }
            }
        }

        return $origin;
    }

    /**
     * Custom thorw error
     * @param int $code
     * @param string $message
     * @return void
     * @throws HttpException
     */
    static public function trigger(int $code, string $message): void
    {
        throw new HttpException($message, $code);
    }
}
