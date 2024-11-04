<?php

namespace App\Filament\Exports;

use App\Models\Product;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class ProductExporter extends Exporter
{
    protected static ?string $model = Product::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('name')
                ->label(__('resources.product.name')),
            ExportColumn::make('category.name')
                ->label(__('resources.product.category')),
            ExportColumn::make('barcode')
                ->label(__('resources.product.barcode')),
            ExportColumn::make('description')
                ->label(__('resources.product.description')),
            ExportColumn::make('price')
                ->label(__('resources.product.price')),
            ExportColumn::make('created_at')
                ->label(__('resources.product.created_at')),
            ExportColumn::make('updated_at')
                ->label(__('resources.product.updated_at')),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = __('resources.product.notifications.export.completed', ['count' => number_format($export->successful_rows)]);

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . __('resources.product.notifications.export.failed', ['count' => number_format($failedRowsCount)]);
        }

        return $body;
    }
} 
