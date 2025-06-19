<?php

namespace App\Filament\Pages;

use App\Models\Product;
use Filament\Pages\Page;
use Livewire\Attributes\On;
use Filament\Notifications\Notification;

class ScanBarcode extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-qr-code';

    protected static string $view = 'filament.pages.scan-barcode';

    protected static ?string $navigationLabel = 'Scan';

    public ?string $scannedCode = null;
    public ?string $manualCode = null;
    public ?Product $foundProduct = null;
    public bool $showProductInfo = false;
    public bool $scannerActive = true;

    #[On('codeScanned')]
    public function handleScannedCode(string $code): void
    {
        $this->manualCode = $code; // Set the manual code which is now the combined input
        $this->scannedCode = $code; // Keep for tracking
        $this->searchProduct();
    }

    public function searchProduct(): void
    {
        $code = $this->manualCode ?: $this->scannedCode;

        if (empty($code)) {
            $this->resetProductInfo();
            return;
        }

        $this->foundProduct = Product::with('category')
            ->where('barcode', $code)
            ->first();

        if ($this->foundProduct) {
            $this->showProductInfo = true;
            $this->scannerActive = false; // Hide scanner when product found

            Notification::make()
                ->title(__('scanner.product_found_title'))
                ->body(__('scanner.product_found_message', ['name' => $this->foundProduct->name]))
                ->success()
                ->send();

            $this->dispatch('stopScanner');
        } else {
            $this->resetProductInfo();

            Notification::make()
                ->title(__('scanner.product_not_found_title'))
                ->body(__('scanner.product_not_found_message', ['code' => $code]))
                ->danger()
                ->send();
        }
    }

    public function submitManualCode(): void
    {
        if (empty($this->manualCode)) {
            Notification::make()
                ->title(__('scanner.error_empty_code_title'))
                ->body(__('scanner.error_empty_code_message'))
                ->danger()
                ->send();
            return;
        }

        $this->scannedCode = $this->manualCode;
        $this->searchProduct();
    }

    private function resetProductInfo(): void
    {
        $this->foundProduct = null;
        $this->showProductInfo = false;
    }

    public function clearSearch(): void
    {
        $this->scannedCode = null;
        $this->manualCode = null;
        $this->scannerActive = true; // Show scanner again
        $this->resetProductInfo();

        // Restart the scanner
        $this->dispatch('restartScanner');
    }
}
