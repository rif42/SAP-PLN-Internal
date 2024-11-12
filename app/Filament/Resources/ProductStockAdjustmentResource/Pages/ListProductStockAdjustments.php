<?php

namespace App\Filament\Resources\ProductStockAdjustmentResource\Pages;

use App\Filament\Resources\ProductStockAdjustmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProductStockAdjustments extends ListRecords
{
    protected static string $resource = ProductStockAdjustmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
