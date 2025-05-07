<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class InvalidOtpException extends Exception
{
    public function __construct(string $message = "Kode OTP tidak valid atau kadaluarsa.", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
