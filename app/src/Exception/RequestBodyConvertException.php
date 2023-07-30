<?php

namespace App\Exception;

use RuntimeException;
use Throwable;

class RequestBodyConvertException extends RuntimeException
{
    public function __construct(Throwable $throwable)
    {
        parent::__construct('error while unmarshalling request body', 0, $throwable);
    }
}
