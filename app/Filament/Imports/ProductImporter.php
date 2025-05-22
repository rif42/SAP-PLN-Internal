<?php

namespace App\Filament\Imports;

use App\Models\Product;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class ProductImporter extends Importer
{
    protected static ?string $model = Product::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->label(__('resources.product.name'))
                ->requiredMapping()
                ->rules(['required', 'string', 'max:255']),
            ImportColumn::make('category')
                ->label(__('resources.product.category'))
                ->requiredMapping()
                ->relationship('category', 'name')
                ->rules(['required']),
            ImportColumn::make('barcode')
                ->label(__('resources.product.barcode'))
                ->requiredMapping()
                ->rules(['required', 'string', 'max:100']),
            ImportColumn::make('description')
                ->label(__('resources.product.description'))
                ->requiredMapping()
                ->rules(['required'])
        ];
    }

    public function resolveRecord(): ?Product
    {
        $product = new Product;
        $product->code = 'PRD-'.str_pad((Product::withTrashed()->count() + 1), 5, '0', STR_PAD_LEFT);

        return $product;
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = __('resources.product.notifications.import.completed', ['count' => number_format($import->successful_rows)]);

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' '.__('resources.product.notifications.import.failed', ['count' => number_format($failedRowsCount)]);
        }

        return $body;
    }
}
