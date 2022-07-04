<?php

declare(strict_types=1);

namespace Xgbnl\Business\Decorates;

use Xgbnl\Business\Attributes\BusinessTag;
use Xgbnl\Business\Decorates\Contacts\Decorate;
use Xgbnl\Business\Decorates\Contacts\ImageObjectDecorate;

#[BusinessTag('字符串包装器')]
class StringDecorate extends ArrayDecorate implements Decorate, ImageObjectDecorate
{
    public function filter(array $origin, mixed $fields): array
    {
        if (isset($origin[$fields])) {
            unset($origin[$fields]);
        }

        return $origin;
    }

    public function arrayFields(array $origin, mixed $fields): array
    {
        return (!isset($origin[$fields])) ? [] : [$fields => $origin[$fields]];
    }

    public function endpoint(mixed $need, string $domain): mixed
    {
        return $domain . $need;
    }

    public function removeEndpoint(mixed $need, string $domain): string
    {
        return $this->replace($need, $domain);
    }
}