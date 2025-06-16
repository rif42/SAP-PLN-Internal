<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ContractStatus: string implements HasLabel
{
    case Canceled = 'canceled';
    case Pending = 'pending';
    case Active = 'active';
    case Done = 'done';
    case Deal = 'Deal'; // Changed from 'deal' to 'Deal' to match exactly what might be in the database

    public function getLabel(): string
    {
        return match($this) {
            self::Canceled => 'Dibatalkan',
            self::Pending => 'Menunggu',
            self::Active => 'Aktif',
            self::Done => 'Selesai',
            self::Deal => 'Deal',
        };
    }
}


