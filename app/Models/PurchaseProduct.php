<?php

namespace App\Models;

use App\Enums\ProductStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
class PurchaseProduct extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'purchase_id',
        'product_id',
        'price',
        'quantity',
        'status',
        'status_at',
    ];

    protected $casts = [
        'status' => ProductStatus::class,
        'status_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if ($model->status && !$model->status_at) {
                $model->status_at = now();
            }
        });

        static::updating(function ($model) {
            if ($model->isDirty('status')) {
                $model->status_at = now();
            }
        });
    }

    public function purchase(): BelongsTo
    {
        return $this->belongsTo(Purchase::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
