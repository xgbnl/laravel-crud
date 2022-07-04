<?php

declare(strict_types=1);

namespace Xgbnl\Business\Utils;

use Throwable;
use Xgbnl\Business\Enum\ResponseEnum;
use Xgbnl\Business\Exceptions\CustomException;

final class Fail
{
    /**
     * @throws CustomException
     */
    final static public function throwFailException(string $message, int $code = ResponseEnum::SERVER_ERROR, Throwable $throwable = null)
    {
        throw new CustomException( $message, $code,$throwable);
    }
}