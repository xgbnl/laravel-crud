<?php

declare(strict_types=1);

namespace Xgbnl\Business\Decorates;

use Xgbnl\Business\Attributes\BusinessTag;
use Xgbnl\Business\Decorates\Contacts\Decorate;

#[BusinessTag('字符串包装器')]
class StringDecorate implements Decorate
{
    public function filter(array &$origin, mixed $fields): array
    {
        if (isset($origin[$fields])) {
            unset($origin[$fields]);
        }

        return $origin;
    }

    public function arrayFields(array $origin, mixed $fields): array
    {
        if (!isset($origin[$fields])) {
            return [];
        }

        return [$fields => $origin[$fields]];
    }
}