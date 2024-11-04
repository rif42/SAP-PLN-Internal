<?php

namespace App\Filament\Imports;

use App\Models\Supplier;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class SupplierImporter extends Importer
{
    protected static ?string $model = Supplier::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->label(__('resources.supplier.name'))
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('contact_info')
                ->label(__('resources.supplier.contact_info'))
                ->requiredMapping()
                ->rules(['required']),
        ];
    }

    public function resolveRecord(): ?Supplier
    {
        return new Supplier();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = __('resources.supplier.notifications.import.completed', ['count' => number_format($import->successful_rows)]);

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . __('resources.supplier.notifications.import.failed', ['count' => number_format($failedRowsCount)]);
        }

        return $body;
    }
} 
