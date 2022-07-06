<?php

namespace Xgbnl\Business\Decorates;

abstract class AbstractDecorate
{
    // 替换掉域名
    final protected function replace(string $haystack, string $domain, string $needle = '/'): string
    {
        $haystack = str_contains($haystack, $domain) ? str_replace($domain, '', $haystack) : $haystack;

        if (str_starts_with($haystack, $needle)) {
            $haystack = substr($haystack, 1, strlen($haystack) - 1);
        }
        return $haystack;
    }

    // 为域或末尾添加`/`
    final protected function appendStr(string $domain, string $needle = '/'): string
    {
        return !str_ends_with($domain, $needle) ? $domain . '/' : $domain;
    }
}