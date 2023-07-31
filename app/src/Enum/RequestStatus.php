<?php

namespace App\Enum;

enum RequestStatus: string
{
    case Active = 'active';
    case Resolved = 'resolve';

    /**
     * @return array<string>
     */
    public static function toArray(): array
    {
        return array_column(RequestStatus::cases(), 'value');
    }
}
