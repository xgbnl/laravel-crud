<?php

namespace Xgbnl\Business\Decorates\Contacts;

interface ImageObjectDecorate extends Decorate
{
    public function endpoint(mixed $need, string $domain): mixed;

    public function removeEndpoint(mixed $need, string $domain): mixed;
}