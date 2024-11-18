<?php

namespace App\Models;

use App\Enums\ProductStatus;
use App\Enums\ProductStockStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Movement extends Model
{
    use LogsActivity, SoftDeletes;

    protected $fillable = [
        'date',
        'product_id',
        'type',
        'quantity',
        'description',
        'status',
        'status_at',
    ];

    protected $casts = [
        'date' => 'date',
        'quantity' => 'integer',
        'type' => ProductStockStatus::class,
        'status' => ProductStatus::class,
        'status_at' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['date', 'product.name', 'type', 'quantity', 'description'])
            ->logOnlyDirty();
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
