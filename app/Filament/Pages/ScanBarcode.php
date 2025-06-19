<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class ScanBarcode extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-qr-code';

    protected static string $view = 'filament.pages.scan-barcode';

    protected static ?string $navigationLabel = 'Scan';
}
