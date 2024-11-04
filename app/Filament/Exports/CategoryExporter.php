<?php

namespace App\Filament\Exports;

use App\Models\Category;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class CategoryExporter extends Exporter
{
    protected static ?string $model = Category::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('name')
                ->label(__('resources.category.name')),
            ExportColumn::make('description')
                ->label(__('resources.category.description')),
            ExportColumn::make('created_at')
                ->label(__('resources.category.created_at')),
            ExportColumn::make('updated_at')
                ->label(__('resources.category.updated_at')),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = __('resources.category.notifications.export.completed', ['count' => number_format($export->successful_rows)]);

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . __('resources.category.notifications.export.failed', ['count' => number_format($failedRowsCount)]);
        }

        return $body;
    }
}
