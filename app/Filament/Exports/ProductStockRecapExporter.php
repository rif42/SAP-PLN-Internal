<?php

namespace App\Filament\Exports;

use App\Models\ProductStockRecap;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class ProductStockRecapExporter extends Exporter
{
    protected static ?string $model = ProductStockRecap::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('date')
                ->label(__('resources.product_stock_recap.date')),
            ExportColumn::make('product.name')
                ->label(__('resources.product_stock_recap.product')),
            ExportColumn::make('quantity')
                ->label(__('resources.product_stock_recap.quantity')),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = __('resources.product_stock_recap.notifications.export.completed', ['count' => number_format($export->successful_rows)]);

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . __('resources.product_stock_recap.notifications.export.failed', ['count' => number_format($failedRowsCount)]);
        }

        return $body;
    }
}
