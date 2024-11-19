<?php

namespace App\Filament\Resources\MovementResource\Pages;

use App\Filament\Resources\MovementResource;
use App\Models\Product;
use App\Models\ProductStockLog;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateMovement extends CreateRecord
{
    protected static string $resource = MovementResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        // Create the movement record
        $movement = parent::handleRecordCreation($data);

        // Find the product
        $product = Product::findOrFail($data['product_id']);

        // Update product stock based on movement type
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
            'causer_id' => $movement->id,
        ]);

        return $movement;
    }
}
