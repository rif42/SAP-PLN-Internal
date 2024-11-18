<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use LogsActivity, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'category_id',
        'barcode',
        'description',
        'price',
        'stock',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'category.name', 'barcode', 'description', 'price', 'stock'])
            ->logOnlyDirty();
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class);
    }

    public function purchaseProducts(): HasMany
    {
        return $this->hasMany(PurchaseProduct::class);
    }

    public function contractItems(): HasMany
    {
        return $this->hasMany(ContractItem::class);
    }

    public function procurementProducts(): HasMany
    {
        return $this->hasMany(ProcurementProduct::class);
    }

    public function invoiceProducts(): HasMany
    {
        return $this->hasMany(InvoiceProduct::class);
    }

    public function movements(): HasMany
    {
        return $this->hasMany(Movement::class);
    }

    public function stockAdjustments(): HasMany
    {
        return $this->hasMany(ProductStockAdjustment::class);
    }

    public function stockLogs(): HasMany
    {
        return $this->hasMany(ProductStockLog::class);
    }

    public function stockRecaps(): HasMany
    {
        return $this->hasMany(ProductStockRecap::class);
    }

    public function shippingDocumentProducts(): HasMany
    {
        return $this->hasMany(ShippingDocumentProduct::class);
    }
}
