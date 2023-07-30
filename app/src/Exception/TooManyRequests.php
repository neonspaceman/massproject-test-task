<?php

namespace App\Exception;

use RuntimeException;

class TooManyRequests extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Too many request');
    }
}
