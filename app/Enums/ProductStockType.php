<?php

namespace App\Enums;

enum ProductStockType: string
{
    case IN = 'in';
    case OUT = 'out';

    public function label(): string
    {
        return match($this) {
            self::IN => 'Masuk',
            self::OUT => 'Keluar',
        };
    }
}
