<x-filament-panels::page>
    <div class="space-y-8">
        <x-filament::section>
            <div class="space-y-6">
                <div id="reader" class="rounded-lg border-2 border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm"></div>
                
                <div wire:ignore class="max-w-xl mx-auto">
                    <x-filament::input.wrapper>
                        <x-filament::input 
                            type="text"
                            id="result"
                            wire:model.live="scannedCode"
                            placeholder="Scanned code will appear here..."
                            readonly
                            class="text-lg font-mono bg-gray-50 dark:bg-gray-800 border-gray-300 dark:border-gray-700 focus:ring-primary-500 focus:border-primary-500"
                        />
                    </x-filament::input.wrapper>
                </div>
            </div>
        </x-filament::section>
    </div>

    @push('scripts')
    <script src="https://unpkg.com/html5-qrcode"></script>
    <script>
        function onScanSuccess(decodedText, decodedResult) {
            document.getElementById('result').value = decodedText;
            Livewire.dispatch('codeScanned', { code: decodedText });
        }

        let html5QrcodeScanner = new Html5QrcodeScanner(
            "reader", 
            { 
                fps: 10,
                qrbox: {width: 400, height: 150},
                rememberLastUsedCamera: true,
                showTorchButtonIfSupported: true,
                aspectRatio: 1.777778,
                formatsToSupport: [
                    Html5QrcodeSupportedFormats.CODE_128,
                    Html5QrcodeSupportedFormats.EAN_13,
                    Html5QrcodeSupportedFormats.EAN_8,
                    Html5QrcodeSupportedFormats.UPC_A,
                    Html5QrcodeSupportedFormats.UPC_E,
                    Html5QrcodeSupportedFormats.CODE_39,
                    Html5QrcodeSupportedFormats.CODE_93,
                    Html5QrcodeSupportedFormats.CODABAR,
                    Html5QrcodeSupportedFormats.ITF
                ]
            }
        );
        html5QrcodeScanner.render(onScanSuccess);
    </script>
    @endpush
</x-filament-panels::page>
