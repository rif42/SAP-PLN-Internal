<?php

namespace App\Models;

use App\Enums\ProductStockStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ProductStockAdjustment extends Model
{
    use LogsActivity;

    protected $fillable = [
        'product_id',
        'quantity',
        'reason',
        'type',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'type' => ProductStockStatus::class,
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['product.name', 'quantity', 'reason'])
            ->logOnlyDirty();
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
