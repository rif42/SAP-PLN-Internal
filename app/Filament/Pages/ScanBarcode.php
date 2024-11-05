<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class ScanBarcode extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.scan-barcode';
}
