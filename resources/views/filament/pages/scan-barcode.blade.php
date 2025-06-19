<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Scanner Section -->
        <x-filament::section>
            <x-slot name="heading">
                {{ __('scanner.page_title') }}
            </x-slot>

            <div class="space-y-6">
                @if($scannerActive)
                <div id="reader"
                    class="rounded-lg border border-gray-300 dark:border-gray-600 overflow-hidden shadow-sm"></div>
                @endif

                <div class="max-w-xl mx-auto space-y-4">
                    <!-- Combined Input Field -->
                    <div class="space-y-3">
                        <div class="text-center">
                            <span class="text-sm text-gray-600 dark:text-gray-400 font-medium">
                                {{ __('scanner.input_label') }}
                            </span>
                        </div>

                        <form wire:submit="submitManualCode" class="flex gap-2">
                            <x-filament::input.wrapper class="flex-1">
                                <x-filament::input
                                    type="text"
                                    id="result"
                                    wire:model.live="manualCode"
                                    placeholder="{{ __('scanner.input_placeholder') }}"
                                    class="text-lg font-mono" />
                            </x-filament::input.wrapper>

                            <x-filament::button type="submit" color="primary">
                                {{ __('scanner.search_button') }}
                            </x-filament::button>
                        </form>
                    </div>
                </div>

                @if($scannedCode || $manualCode)
                <div class="text-center">
                    <x-filament::button wire:click="clearSearch" color="gray" size="md" class="inline-flex items-center">
                        {{ __('scanner.clear_button') }}
                    </x-filament::button>
                </div>
                @endif
            </div>
        </x-filament::section>

        <!-- Product Information Section -->
        @if($showProductInfo && $foundProduct)
        <x-filament::section class="mt-6">
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    {{ __('scanner.product_info_title') }}
                </div>
            </x-slot>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <div>
                        <x-filament::section>
                            <x-slot name="heading">
                                {{ __('scanner.product_detail_title') }}
                            </x-slot>

                            <div class="space-y-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        {{ __('scanner.product_code_label') }}
                                    </label>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-gray-100 font-mono bg-gray-50 dark:bg-gray-800 p-2 rounded border">
                                        {{ $foundProduct->code }}
                                    </p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        {{ __('scanner.product_name_label') }}
                                    </label>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-gray-100 font-semibold bg-gray-50 dark:bg-gray-800 p-2 rounded border">
                                        {{ $foundProduct->name }}
                                    </p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        {{ __('scanner.barcode_label') }}
                                    </label>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-gray-100 font-mono bg-gray-50 dark:bg-gray-800 p-2 rounded border">
                                        {{ $foundProduct->barcode }}
                                    </p>
                                </div>
                            </div>
                        </x-filament::section>
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <x-filament::section>
                            <x-slot name="heading">
                                {{ __('scanner.additional_info_title') }}
                            </x-slot>

                            <div class="space-y-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        {{ __('scanner.category_label') }}
                                    </label>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-gray-100 bg-gray-50 dark:bg-gray-800 p-2 rounded border">
                                        {{ $foundProduct->category?->name ?? __('scanner.no_category') }}
                                    </p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        {{ __('scanner.specification_label') }}
                                    </label>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-gray-100 bg-gray-50 dark:bg-gray-800 p-2 rounded border">
                                        {{ $foundProduct->specification ?? __('scanner.no_specification') }}
                                    </p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        {{ __('scanner.stock_label') }}
                                    </label>
                                    <p class="mt-1 text-sm font-semibold bg-gray-50 dark:bg-gray-800 p-2 rounded border
                                            @if($foundProduct->stock > 0) 
                                                text-success-600 dark:text-success-400
                                            @elseif($foundProduct->stock == 0)
                                                text-warning-600 dark:text-warning-400
                                            @else
                                                text-danger-600 dark:text-danger-400
                                            @endif">
                                        {{ number_format($foundProduct->stock) }} {{ __('scanner.stock_unit') }}
                                    </p>
                                </div>
                            </div>
                        </x-filament::section>
                    </div>
                </div>
            </div>
        </x-filament::section>
        @endif

        <!-- No Product Found Message -->
        @if(($scannedCode || $manualCode) && !$showProductInfo)
        <x-filament::section class="mt-6">
            <div class="text-center py-6">
                <x-heroicon-o-exclamation-triangle class="w-8 h-8 text-danger-500 mx-auto mb-3" />
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">
                    {{ __('scanner.product_not_found_title') }}
                </h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                    {{ __('scanner.not_found_description') }} <span class="font-mono font-semibold">{{ $scannedCode ?: $manualCode }}</span>
                </p>
            </div>
        </x-filament::section>
        @endif
    </div>

    @push('scripts')
    <script src="{{ asset('js/html5-qrcode.min.js') }}"></script>

    <script>
        let html5QrcodeScanner = null;

        function initializeScanner() {
            if (html5QrcodeScanner) {
                html5QrcodeScanner.clear();
            }

            html5QrcodeScanner = new Html5QrcodeScanner(
                "reader", {
                    fps: 10,
                    qrbox: {
                        width: 400,
                        height: 150
                    },
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
        }

        function onScanSuccess(decodedText, decodedResult) {
            // Update both the visible input and the Livewire property
            document.getElementById('result').value = decodedText;

            // Dispatch to Livewire
            Livewire.dispatch('codeScanned', {
                code: decodedText
            });
        }

        // Initialize scanner when page loads
        document.addEventListener('DOMContentLoaded', function() {
            if (document.getElementById('reader')) {
                initializeScanner();
            }
        });

        // Listen for Livewire events
        document.addEventListener('livewire:init', () => {
            Livewire.on('stopScanner', () => {
                if (html5QrcodeScanner) {
                    html5QrcodeScanner.clear().then(() => {
                        console.log('Scanner stopped');
                    }).catch(err => {
                        console.log('Error stopping scanner:', err);
                    });
                }
            });

            Livewire.on('restartScanner', () => {
                setTimeout(() => {
                    if (document.getElementById('reader')) {
                        initializeScanner();
                    }
                }, 100);
            });
        });
    </script>
    @endpush
</x-filament-panels::page>
