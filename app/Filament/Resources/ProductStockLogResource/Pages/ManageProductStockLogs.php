<?php

namespace App\Filament\Resources\ProductStockLogResource\Pages;

use App\Filament\Exports\ProductStockLogExporter;
use App\Filament\Resources\ProductStockLogResource;
use Filament\Actions;
use Filament\Actions\ExportAction;
use Filament\Resources\Pages\ManageRecords;

class ManageProductStockLogs extends ManageRecords
{
    protected static string $resource = ProductStockLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ExportAction::make()->exporter(ProductStockLogExporter::class),
            Actions\CreateAction::make(),
        ];
    }
}
