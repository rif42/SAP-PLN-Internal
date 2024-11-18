<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductStockRecap extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'date',
        'product_id',
        'quantity'
    ];

    protected $casts = [
        'date' => 'date',
        'quantity' => 'integer'
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
