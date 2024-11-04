<?php

namespace App\Filament\Resources\SupplierResource\Pages;

use App\Filament\Exports\SupplierExporter;
use App\Filament\Imports\SupplierImporter;
use App\Filament\Resources\SupplierResource;
use Filament\Actions;
use Filament\Actions\ExportAction;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ManageRecords;

class ManageSuppliers extends ManageRecords
{
    protected static string $resource = SupplierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ExportAction::make()->exporter(SupplierExporter::class),
            ImportAction::make()->importer(SupplierImporter::class),
            Actions\CreateAction::make(),
        ];
    }
}
