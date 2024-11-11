<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ContractStatus: string implements HasLabel
{
    case Pending = 'pending';
    case Active = 'active';
    case Inactive = 'inactive';

    public function getLabel(): string
    {
        return match($this) {
            self::Pending => 'Menunggu',
            self::Active => 'Aktif',
            self::Inactive => 'Tidak Aktif',
        };
    }
}
