<?php

namespace App\Filament\Exports;

use App\Models\User;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class UserExporter extends Exporter
{
    protected static ?string $model = User::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('name')
                ->label(__('resources.user.name')),
            ExportColumn::make('email')
                ->label(__('resources.user.email')),
            ExportColumn::make('created_at')
                ->label(__('resources.user.created_at')),
            ExportColumn::make('updated_at')
                ->label(__('resources.user.updated_at')),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = __('resources.user.notifications.export.completed', ['count' => number_format($export->successful_rows)]);

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . __('resources.user.notifications.export.failed', ['count' => number_format($failedRowsCount)]);
        }

        return $body;
    }
} 
