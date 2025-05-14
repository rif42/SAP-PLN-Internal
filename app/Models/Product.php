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
        'stock',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'category.name', 'barcode', 'description', 'stock'])
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

    public function contractProducts(): HasMany
    {
        return $this->hasMany(ContractProduct::class);
    }

    protected static function booted()
    {
        static::created(function ($product) {
            // Buat rekap stok untuk hari ini saat produk baru dibuat
            \App\Models\ProductStockRecap::create([
                'date' => \Carbon\Carbon::today()->format('Y-m-d'),
                'product_id' => $product->id,
                'quantity' => $product->stock ?? 0,
            ]);
        });
    }
}


