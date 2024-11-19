<?php

namespace App\Filament\Resources\ProductStockAdjustmentResource\Pages;

use App\Filament\Resources\ProductStockAdjustmentResource;
use App\Models\Product;
use App\Models\ProductStockLog;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateProductStockAdjustment extends CreateRecord
{
    protected static string $resource = ProductStockAdjustmentResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        // Create the stock adjustment record
        $stockAdjustment = parent::handleRecordCreation($data);

        // Find the product
        $product = Product::findOrFail($data['product_id']);

        // Update product stock based on adjustment type
        if ($data['type'] === 'IN') {
            $product->increment('stock', $data['quantity']);
        } else {
            $product->decrement('stock', $data['quantity']);
        }

        // Create a stock log entry
        ProductStockLog::create([
            'product_id' => $product->id,
            'quantity' => $data['quantity'],
            'type' => $data['type'],
            'causer_type' => self::class,
            'causer_id' => $stockAdjustment->id,
        ]);

        return $stockAdjustment;
    }
}
