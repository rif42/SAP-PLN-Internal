<?php

namespace App\Filament\Resources\ProductStockAdjustmentResource\Pages;

use App\Filament\Resources\ProductStockAdjustmentResource;
use App\Models\Product;
use App\Models\ProductStockLog;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditProductStockAdjustment extends EditRecord
{
    protected static string $resource = ProductStockAdjustmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->before(function (Model $record) {
                    // Find the product
                    $product = Product::findOrFail($record->product_id);

                    // Revert the stock adjustment
                    if ($record->type === 'IN') {
                        $product->decrement('stock', $record->quantity);
                    } else {
                        $product->increment('stock', $record->quantity);
                    }

                    // Remove the corresponding stock log
                    ProductStockLog::where('causer_type', self::class)
                        ->where('causer_id', $record->id)
                        ->delete();
                }),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        // Find the product
        $product = Product::findOrFail($data['product_id']);

        // Revert the previous stock adjustment
        $previousAdjustment = $record;
        if ($previousAdjustment->type === 'IN') {
            $product->decrement('stock', $previousAdjustment->quantity);
        } else {
            $product->increment('stock', $previousAdjustment->quantity);
        }

        // Update product stock based on new adjustment type
        if ($data['type'] === 'IN') {
            $product->increment('stock', $data['quantity']);
        } else {
            $product->decrement('stock', $data['quantity']);
        }

        // Update the stock log entry
        $stockLog = ProductStockLog::where('causer_type', self::class)
            ->where('causer_id', $record->id)
            ->first();

        if ($stockLog) {
            $stockLog->update([
                'quantity' => $data['quantity'],
                'type' => $data['type'],
            ]);
        } else {
            // Create a new stock log entry if not found
            ProductStockLog::create([
                'product_id' => $product->id,
                'quantity' => $data['quantity'],
                'type' => $data['type'],
                'causer_type' => self::class,
                'causer_id' => $record->id,
            ]);
        }

        // Update the stock adjustment record
        return parent::handleRecordUpdate($record, $data);
    }
}
