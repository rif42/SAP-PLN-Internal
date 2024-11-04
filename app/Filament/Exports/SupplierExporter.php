<?php

namespace App\Filament\Exports;

use App\Models\Supplier;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class SupplierExporter extends Exporter
{
    protected static ?string $model = Supplier::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('name')
                ->label(__('resources.supplier.name')),
            ExportColumn::make('contact_info')
                ->label(__('resources.supplier.contact_info')),
            ExportColumn::make('created_at')
                ->label(__('resources.supplier.created_at')),
            ExportColumn::make('updated_at')
                ->label(__('resources.supplier.updated_at')),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = __('resources.supplier.notifications.export.completed', ['count' => number_format($export->successful_rows)]);

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . __('resources.supplier.notifications.export.failed', ['count' => number_format($failedRowsCount)]);
        }

        return $body;
    }
} 
