<?php

declare(strict_types=1);

namespace Xgbnl\Business\Utils;

use Illuminate\Http\JsonResponse;
use InvalidArgumentException;
use Xgbnl\Business\Decorates\Factory\DecorateFactory;
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
     * @param bool $returnOrigin
     * @return array
     */
    static public function filterFields(array $origin, mixed $fields, bool $returnOrigin = true): array
    {
        $decorate = DecorateFactory::builderDecorate($fields);

        return $returnOrigin ? $decorate->filter($origin, $fields) : $decorate->arrayFields($origin, $fields);
    }

    /**
     * Custom thorw error
     * @param int $code
     * @param string $message
     * @return void
     */
    static public function trigger(int $code, string $message): void
    {
        throw new InvalidArgumentException($message, $code);
    }

    /**
     * Remove or add domain to image path.
     * @param mixed $needle
     * @param string|null $domain
     * @param bool $replace
     * @return mixed
     */
    static public function endpoint(mixed $needle, string $domain = null, bool $replace = false): mixed
    {
        $decorate = DecorateFactory::builderDecorate($needle);

        return $replace ? $decorate->removeEndpoint($needle, $domain) : $decorate->endpoint($needle, $domain);
    }
}
