<?php

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class DataAccessException extends Exception
{
    public function __construct(string $message = 'Terjadi kesalahan saat mengakses data.', int $code = Response::HTTP_INTERNAL_SERVER_ERROR, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
