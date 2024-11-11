<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use LogsActivity, SoftDeletes;

    protected $fillable = [
        'name',
        'sales_name',
        'sales_phone',
        'sales_email',
        'logistics_name',
        'logistics_phone',
        'logistics_email',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'sales_name', 'sales_phone', 'sales_email', 'logistics_name', 'logistics_phone', 'logistics_email'])
            ->logOnlyDirty();
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class);
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    public function procurements(): HasMany
    {
        return $this->hasMany(Procurement::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }
}
