<?php

declare(strict_types=1);

namespace Xgbnl\Business\Decorates;

use Xgbnl\Business\Attributes\BusinessTag;
use Xgbnl\Business\Decorates\Contacts\Decorate;
use Xgbnl\Business\Decorates\Contacts\ImageObjectDecorate;

#[BusinessTag('数组包装器')]
class ArrayDecorate extends AbstractDecorate implements Decorate, ImageObjectDecorate
{
    public function filter(array $origin, mixed $fields): array
    {
        foreach ($fields as $field) {
            if (isset($origin[$field])) {
                unset($origin[$field]);
            }
        }
        unset($field);

        return $origin;
    }

    public function arrayFields(array $origin, mixed $fields): array
    {
        $items = [];

        array_map(function ($field) use ($origin, &$items) {
            if (isset($origin[$field])) {
                $items[$field] = $origin[$field];
            }
        }, $fields);

        return $items;
    }

    public function endpoint(mixed $need, string $domain): mixed
    {
        return array_map(fn($path) => $this->appendStr($domain) . $path, $need);
    }

    public function removeEndpoint(mixed $need, string $domain): mixed
    {
        return array_map(fn($path) => $this->replace($path, $domain), $need);
    }
}