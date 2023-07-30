<?php

namespace App\Enum;

enum RequestStatus: string
{
    case Active = 'active';
    case Resolved = 'resolve';
}
