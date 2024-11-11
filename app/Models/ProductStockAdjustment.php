<?php

namespace App\Models;

use App\Enums\ProductStockAdjustmentType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class ProductStockAdjustment extends Model
{
    use LogsActivity;

    protected $fillable = [
        'product_id',
        'quantity',
        'reason',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'type' => ProductStockAdjustmentType::class,
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
