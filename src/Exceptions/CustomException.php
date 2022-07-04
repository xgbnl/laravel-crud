<?php

namespace Xgbnl\Business\Exceptions;

use InvalidArgumentException;
use Throwable;

class CustomException extends InvalidArgumentException
{
    private string $msg;
    private int $status;

    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        $this->msg = $message;

        $this->status = $code;
        parent::__construct($message, $code, $previous);
    }
}