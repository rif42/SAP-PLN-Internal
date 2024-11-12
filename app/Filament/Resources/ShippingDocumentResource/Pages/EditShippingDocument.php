<?php

namespace App\Filament\Resources\ShippingDocumentResource\Pages;

use App\Filament\Resources\ShippingDocumentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditShippingDocument extends EditRecord
{
    protected static string $resource = ShippingDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
