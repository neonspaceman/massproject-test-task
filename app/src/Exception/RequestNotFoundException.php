<?php

namespace App\Exception;

use RuntimeException;

class RequestNotFoundException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Request not found');
    }
}
