<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ContractStatus: string implements HasLabel
{
    case Canceled = 'canceled';
    case Pending = 'pending';
    case Active = 'active';
    case Done = 'done';

    public function getLabel(): string
    {
        return match($this) {
            self::Canceled => 'Dibatalkan',
            self::Pending => 'Menunggu',
            self::Active => 'Aktif',
            self::Done => 'Selesai',
        };
    }
}
