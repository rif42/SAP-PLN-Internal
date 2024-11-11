<?php

namespace App\Enums;

enum ContractStatus: string
{
    case Pending = 'pending';
    case Active = 'active';
    case Inactive = 'inactive';

    public function label(): string
    {
        return match($this) {
            self::Pending => 'Menunggu',
            self::Active => 'Aktif',
            self::Inactive => 'Tidak Aktif',
        };
    }
}
