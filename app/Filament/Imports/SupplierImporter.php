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
            ImportColumn::make('sales_name')
                ->label(__('resources.supplier.sales_name'))
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('sales_phone')
                ->label(__('resources.supplier.sales_phone'))
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('sales_email')
                ->label(__('resources.supplier.sales_email'))
                ->rules(['email', 'nullable']),
            ImportColumn::make('logistics_name')
                ->label(__('resources.supplier.logistics_name'))
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('logistics_phone')
                ->label(__('resources.supplier.logistics_phone'))
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('logistics_email')
                ->label(__('resources.supplier.logistics_email'))
                ->rules(['email', 'nullable']),
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
