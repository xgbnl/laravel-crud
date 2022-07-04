<?php

namespace Xgbnl\Business\Attributes;

use Attribute;

#[Attribute]
class BusinessTag
{
    public string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }
}