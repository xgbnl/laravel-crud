<?php

declare(strict_types=1);

namespace Xgbnl\Business\Utils;

use Throwable;
use Xgbnl\Business\Enum\ResponseEnum;
use HttpRuntimeException;

final class Fail
{
    /**
     * @throws HttpException
     * @throws HttpRuntimeException
     */
    final static public function throwFailException(string $message, int $code = ResponseEnum::SERVER_ERROR, Throwable $throwable = null)
    {
        throw new HttpRuntimeException( $message, $code,$throwable);
    }
}