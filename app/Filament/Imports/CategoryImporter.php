<?php

namespace App\Filament\Imports;

use App\Models\Category;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class CategoryImporter extends Importer
{
    protected static ?string $model = Category::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->label(__('resources.category.name'))
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('description')
                ->label(__('resources.category.description')),
        ];
    }

    public function resolveRecord(): ?Category
    {
        return new Category();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = __('resources.category.notifications.import.completed', ['count' => number_format($import->successful_rows)]);

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . __('resources.category.notifications.import.failed', ['count' => number_format($failedRowsCount)]);
        }

        return $body;
    }
}
