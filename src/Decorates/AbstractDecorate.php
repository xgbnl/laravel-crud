<?php

namespace Xgbnl\Business\Decorates;

abstract class AbstractDecorate
{
    final protected function replace(string $haystack, string $domain): string
    {
        return str_contains($haystack, $domain) ? str_replace($domain, '', $haystack) : $haystack;
    }

    final protected function appendStr(string $domain, string $needle = '/'): string
    {
        return !str_ends_with($domain, $needle) ? $domain . '/' : $domain;
    }
}