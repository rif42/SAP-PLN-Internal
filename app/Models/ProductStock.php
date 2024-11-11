<?php

namespace App\Models;

use App\Enums\ProductStockType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductStock extends Model
{
    use LogsActivity, SoftDeletes;

    protected $fillable = [
        'product_id',
        'quantity',
        'type',
    ];

    protected $casts = [
        'type' => ProductStockType::class,
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['product.name', 'quantity', 'type']);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
