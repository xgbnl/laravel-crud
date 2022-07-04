<?php

namespace Xgbnl\Business\Decorates\Factory;

use Xgbnl\Business\Decorates\ArrayDecorate;
use Xgbnl\Business\Decorates\Contacts\Decorate;
use Xgbnl\Business\Decorates\StringDecorate;

class DecorateFactory
{
    static public function builderDecorate(mixed $type): Decorate
    {
        return match (true) {
            is_string($type) => new StringDecorate(),
            is_array($type)  => new ArrayDecorate(),
        };
    }
}