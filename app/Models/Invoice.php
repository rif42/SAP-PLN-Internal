<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Invoice extends Model
{
    use LogsActivity, SoftDeletes;

    protected $fillable = [
        'code',
        'number',
        'date',
        'purchase_id',
        'supplier_id',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['code', 'number', 'date', 'purchase.code', 'supplier.name'])
            ->logOnlyDirty();
    }

    public function purchase(): BelongsTo
    {
        return $this->belongsTo(Purchase::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(InvoiceProduct::class);
    }

    public function shippingDocuments(): HasMany
    {
        return $this->hasMany(ShippingDocument::class);
    }
}
