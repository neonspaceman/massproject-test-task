<?php

namespace App\Exception;

use RuntimeException;
use Throwable;

class QueryConvertException extends RuntimeException
{
    public function __construct(Throwable $throwable)
    {
        parent::__construct('error while unmarshalling query', 0, $throwable);
    }
}
