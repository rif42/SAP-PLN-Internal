<?php

namespace App\Filament\Resources\ShippingDocumentResource\Pages;

use App\Filament\Resources\ShippingDocumentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListShippingDocuments extends ListRecords
{
    protected static string $resource = ShippingDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
