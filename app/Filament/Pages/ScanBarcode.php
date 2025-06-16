<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Product;

class ScanBarcode extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-qr-code';

    protected static string $view = 'filament.pages.scan-barcode';

    protected static ?string $navigationLabel = 'Scan';

    public $scannedCode;
    public $productName;
    public $productCategory;
    public $productSpecification;

    protected function getScannedCode(): string
    {
        return $this->scannedCode;
    }

    protected function getProductName(): string
    {
        return $this->productName;
    }

    protected function getProductCategory(): string
    {
        return $this->productCategory;
    }

    protected function getProductSpecification(): string
    {
        return $this->productSpecification;
    }

    protected function getListeners(): array
    {
        return [
            'codeScanned' => 'onCodeScanned',
        ];
    }

      public function onCodeScanned(string $code): void
    {
        $this->scannedCode = $code;

        $product = Product::where('barcode', $code)->first();

        if ($product) {
            $this->productName = $product->name;
            $this->productCategory = $product->category->name ?? 'Tidak diketahui';
            $this->productSpecification = $product->description ?? '-';
        } else {
            $this->productName = 'Produk tidak ditemukan';
            $this->productCategory = '-';
            $this->productSpecification = '-';
        }
    }


}
