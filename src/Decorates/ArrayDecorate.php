<?php

declare(strict_types=1);

namespace Xgbnl\Business\Decorates;

use Xgbnl\Business\Attributes\BusinessTag;
use Xgbnl\Business\Decorates\Contacts\Decorate;

#[BusinessTag('数组包装器')]
class ArrayDecorate implements Decorate
{
    public function filter(array &$origin, mixed $fields): array
    {
        foreach ($fields as $field) {
            if (isset($origin[$field])) {
                unset($origin[$field]);
            }
        }
        unset($field);

        return $origin;
    }

    public function arrayFields(array &$origin, mixed $fields): array
    {
        $items = [];

        foreach ($fields as $field) {
            if (isset($origin[$field])) {
                $items[$field] = $origin[$field];
            }
        }
        unset($field);

        return $items;
    }
}