<?php

namespace App\Models;

use App\Enums\ProductStockStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductStockLog extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'product_id',
        'quantity',
        'type',
        'causer_type',
        'causer_id'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'type' => ProductStockStatus::class
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function causer(): MorphTo
    {
        return $this->morphTo();
    }
}
