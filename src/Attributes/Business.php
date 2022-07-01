<?php

namespace Xgbnl\Business\Attributes;

use Attribute;

#[Attribute]
class Business
{
    public array|string $businessModels;

    public function __construct(array|string $businessModels)
    {
        $this->businessModels = $businessModels;
    }
}