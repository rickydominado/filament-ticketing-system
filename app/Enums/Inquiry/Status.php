<?php

namespace App\Enums\Inquiry;

enum Status: int
{
    case Open = 1;
    case Closed = 2;

    public static function statuses(): array
    {
        return array_column(self::cases(), 'name', 'value');
    }
}
