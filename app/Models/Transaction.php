<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Transaction extends Model
{
    use LogsActivity;

    protected $fillable = [
        'product_id',
        'transaction_type',
        'quantity',
        'transaction_date',
    ];

    protected $casts = [
        'transaction_date' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['product.name', 'transaction_type', 'quantity', 'transaction_date'])
            ->logOnlyDirty();
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
