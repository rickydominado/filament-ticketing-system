<?php

namespace App\Enums\Inquiry;

enum Severity: int
{
    case Low = 1;
    case Medium = 2;
    case High = 3;
    case Critical = 4;

    public static function severities(): array
    {
        return array_column(self::cases(), 'name', 'value');
    }
}
