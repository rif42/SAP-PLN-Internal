<?php

namespace App\Models;

use App\Enums\ProductStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProcurementProduct extends Model
{
    use LogsActivity, SoftDeletes;

    protected $fillable = [
        'procurement_id',
        'product_id',
        'price',
        'quantity',
        'status',
        'status_at',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'quantity' => 'integer',
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
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['procurement.code', 'product.name', 'price', 'quantity'])
            ->logOnlyDirty();
    }

    public function procurement(): BelongsTo
    {
        return $this->belongsTo(Procurement::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
