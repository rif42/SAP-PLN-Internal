<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\SoftDeletes;

class Purchase extends Model
{
    use LogsActivity, SoftDeletes;

    protected $fillable = [
        'supplier_id',
        'product_id',
        'purchase_date',
        'quantity',
        'price',
        'procurement_id',
    ];

    protected $casts = [
        'purchase_date' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['supplier.name', 'product.name', 'purchase_date', 'quantity', 'price', 'procurement.name'])
            ->logOnlyDirty();
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function procurement(): BelongsTo
    {
        return $this->belongsTo(Procurement::class);
    }
}
