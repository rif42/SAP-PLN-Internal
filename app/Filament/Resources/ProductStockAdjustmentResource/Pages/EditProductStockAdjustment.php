<?php

namespace App\Filament\Resources\ProductStockAdjustmentResource\Pages;

use App\Filament\Resources\ProductStockAdjustmentResource;
use App\Models\Product;
use App\Models\ProductStockLog;
use App\Enums\ProductStockStatus;
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

                    // Revert the stock adjustment by subtracting the record's quantity
                    // Since quantity is already signed (+ for IN, - for OUT), we subtract it
                    $product->decrement('stock', $record->quantity);

                    // Remove the corresponding stock log
                    ProductStockLog::where('causer_type', self::class)
                        ->where('causer_id', $record->id)
                        ->delete();
                }),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Make quantity always positive in the form
        $data['quantity'] = abs($data['quantity']);

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        // Find the product
        $product = Product::findOrFail($data['product_id']);

        // Get the absolute quantity value (always positive in the form)
        $quantity = abs($data['quantity']);

        // Revert the previous stock adjustment
        // Since quantity is already signed (+ for IN, - for OUT), we subtract it
        $product->decrement('stock', $record->quantity);

        // Prepare the new adjustment data with the appropriate sign
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

        // Update the stock log entry
        $stockLog = ProductStockLog::where('causer_type', self::class)
            ->where('causer_id', $record->id)
            ->first();

        if ($stockLog) {
            $stockLog->update([
                'quantity' => $adjustmentData['quantity'], // Use the signed quantity
                'type' => $data['type'],
            ]);
        } else {
            // Create a new stock log entry if not found
            ProductStockLog::create([
                'product_id' => $product->id,
                'quantity' => $adjustmentData['quantity'], // Use the signed quantity
                'type' => $data['type'],
                'causer_type' => self::class,
                'causer_id' => $record->id,
            ]);
        }

        // Update the stock adjustment record with the signed quantity
        return parent::handleRecordUpdate($record, $adjustmentData);
    }
}





