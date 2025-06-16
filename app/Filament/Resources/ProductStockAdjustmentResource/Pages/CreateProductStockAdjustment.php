<?php

namespace App\Filament\Resources\ProductStockAdjustmentResource\Pages;

use App\Filament\Resources\ProductStockAdjustmentResource;
use App\Models\Product;
use App\Models\ProductStockLog;
use App\Enums\ProductStockStatus;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateProductStockAdjustment extends CreateRecord
{
    protected static string $resource = ProductStockAdjustmentResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        // Find the product
        $product = Product::findOrFail($data['product_id']);

        // Get the absolute quantity value (always positive in the form)
        $quantity = abs($data['quantity']);

        // Create the stock adjustment record with the appropriate sign
        $adjustmentData = $data;
        if ($data['type'] === ProductStockStatus::IN->value) {
            // For stock in, quantity is positive
            $adjustmentData['quantity'] = $quantity;
            // Update product stock (add)
            $product->increment('stock', $quantity);
        } else {
            // For stock out, quantity is negative
            $adjustmentData['quantity'] = -$quantity;
            // Update product stock (subtract)
            $product->decrement('stock', $quantity);
        }

        // Create the record with the signed quantity
        $stockAdjustment = parent::handleRecordCreation($adjustmentData);

        // Create a stock log entry
        ProductStockLog::create([
            'product_id' => $product->id,
            'quantity' => $adjustmentData['quantity'], // Use the signed quantity
            'type' => $data['type'],
            'causer_type' => self::class,
            'causer_id' => $stockAdjustment->id,
        ]);

        return $stockAdjustment;
    }
}




