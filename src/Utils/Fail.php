<?php

declare(strict_types=1);

namespace Xgbnl\Business\Utils;

use Throwable;
use Xgbnl\Business\Enum\ResponseEnum;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class Fail
{
    final static public function throwFailException(string $message, int $code = ResponseEnum::SERVER_ERROR, Throwable $throwable = null)
    {
        throw new HttpException($code, $message, $throwable);
    }
}