<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ProductStockStatus: string implements HasLabel
{
    case IN = 'in';
    case OUT = 'out';


    public function getLabel(): string
    {
        return match ($this) {
            self::IN => 'Masuk',
            self::OUT => 'Keluar',
        };
    }
}

enum ProductStatus: string implements HasLabel
{
    case CANCELED = 'canceled';
    case PENDING = 'pending';
    case DONE = 'done';

    public function getLabel(): string
    {
        return match ($this) {
            self::CANCELED => 'Dibatalkan',
            self::PENDING => 'Menunggu',
            self::DONE => 'Selesai',
        };
    }
}

