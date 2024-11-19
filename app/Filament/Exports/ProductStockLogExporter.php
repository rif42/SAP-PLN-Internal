<?php

namespace App\Filament\Exports;

use App\Models\ProductStockLog;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class ProductStockLogExporter extends Exporter
{
    protected static ?string $model = ProductStockLog::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('product.name')
                ->label(__('resources.product_stock_log.product')),
            ExportColumn::make('quantity')
                ->label(__('resources.product_stock_log.quantity')),
            ExportColumn::make('type')
                ->label(__('resources.product_stock_log.type'))
                ->formatStateUsing(fn ($state) => $state->value),
            ExportColumn::make('causer_type')
                ->label(__('resources.product_stock_log.causer_type')),
            ExportColumn::make('causer_id')
                ->label(__('resources.product_stock_log.causer_id')),
            ExportColumn::make('created_at')
                ->label(__('resources.product_stock_log.created_at')),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = __('resources.product_stock_log.notifications.export.completed', ['count' => number_format($export->successful_rows)]);

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . __('resources.product_stock_log.notifications.export.failed', ['count' => number_format($failedRowsCount)]);
        }

        return $body;
    }
}
