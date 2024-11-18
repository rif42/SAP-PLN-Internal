<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ProductStatus: string implements HasLabel
{
    case CANCELED = 'canceled';
    case PENDING = 'pending';
    case DONE = 'done';

    public function getLabel(): string
    {
        return match ($this) {
            self::CANCELED => 'Dibatalkan',
            self::PENDING => 'Tertunda',
            self::DONE => 'Selesai',
        };
    }
}
