<?php

namespace App\Helpers;

class FormatHelper
{
    public static function brl(float|int|string $valor): string
    {
        return 'R$ ' . number_format((float) $valor, 2, ',', '.');
    }
}
