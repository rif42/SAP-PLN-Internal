<?php

namespace App\Filament\Resources\ProductStockRecapResource\Pages;

use App\Filament\Exports\ProductStockRecapExporter;
use App\Filament\Resources\ProductStockRecapResource;
use Filament\Actions;
use Filament\Actions\ExportAction;
use Filament\Resources\Pages\ManageRecords;

class ManageProductStockRecaps extends ManageRecords
{
    protected static string $resource = ProductStockRecapResource::class;
    
    protected function getHeaderActions(): array
    {
        return [
            ExportAction::make()->exporter(ProductStockRecapExporter::class),
            Actions\CreateAction::make(),
        ];
    }
}
