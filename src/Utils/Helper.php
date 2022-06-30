<?php

namespace Xgbnl\Business\Utils;

class Helper
{
    final static public function strEndWith(string $haystack, string|array $needle): string
    {
        if (is_string($needle)) {
            return str_ends_with($haystack, $needle) ? substr($haystack, 0, -strlen($needle)) : $haystack;
        }

        foreach ($needle as $need) {
            if (str_ends_with($haystack, $need)) {
                return substr($haystack, 0, -strlen($need));
            }
        }

        return $haystack;
    }
}