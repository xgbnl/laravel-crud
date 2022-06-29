<?php

namespace Xgbnl\Business\Enum;

enum ResponseEnum
{
    final public const SERVER_ERROR = 500;

    final public const VALIDATED_ERROR = 422;

    final public const SERVER_OK = 200;

    final public const UNAUTHORIZED = 401;

    final public const FORBIDDEN = 403;
}
