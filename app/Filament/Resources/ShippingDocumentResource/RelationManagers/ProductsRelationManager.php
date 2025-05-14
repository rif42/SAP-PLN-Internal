<?php

namespace App\Filament\Resources\ShippingDocumentResource\RelationManagers;

use App\Enums\ProductStatus;
use App\Enums\ProductStockStatus;
use App\Models\Product;
use App\Models\ProductStockLog;
use App\Models\ShippingDocumentProduct;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductsRelationManager extends RelationManager
{
    protected static string $relationship = 'products';

    protected static ?string $title = 'Barang';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('product_id')
                    ->label(__('resources.shipping_document_product.product'))
                    ->options(function ($get) {
                        $shippingDocument = $this->getOwnerRecord();
                        $invoiceProducts = $shippingDocument->invoice->products;

                        return $invoiceProducts->mapToGroups(function ($invoiceProduct) {
                            return [
                                $invoiceProduct->product->id => [
                                    'id' => $invoiceProduct->product->id,
                                    'label' => sprintf(
                                        '%s - %s',
                                        $invoiceProduct->product->code,
                                        $invoiceProduct->product->name
                                    ),
                                    'quantity' => $invoiceProduct->quantity,
                                ],
                            ];
                        })->map(function ($items) use ($shippingDocument) {
                            // Get the product label
                            $label = $items->first()['label'];

                            // Calculate quantities
                            $totalQuantity = $items->sum('quantity');

                            $usedQuantity = $shippingDocument->products()
                                ->where('product_id', $items->first()['id'])
                                ->sum('quantity');
                            $remainingQuantity = $totalQuantity - $usedQuantity;

                            // Format the option label with quantities
                            return sprintf(
                                '%s (Jumlah: %d, Sisa: %d)',
                                $label,
                                $totalQuantity,
                                $remainingQuantity
                            );
                        });
                    })
                    ->required()
                    ->live()
                    ->rules([
                        function (Get $get, ?ShippingDocumentProduct $record = null) {
                            return function (string $attribute, $value, \Closure $fail) use ($record) {
                                $shippingDocument = $this->getOwnerRecord();

                                // Get total quantity from invoice
                                $invoiceProductQuantity = $shippingDocument->invoice->products()
                                    ->where('product_id', $value)
                                    ->sum('quantity');

                                // Get used quantity from shipping documents
                                $usedQuantity = $shippingDocument->products()
                                    ->where('product_id', $value)
                                    ->sum('quantity');

                                // Add back current quantity if editing existing record
                                if ($record && $record->product_id == $value) {
                                    $usedQuantity -= $record->quantity;
                                }

                                $remainingQuantity = $invoiceProductQuantity - $usedQuantity;

                                if ($remainingQuantity <= 0) {
                                    $fail("Jumlah barang ini sudah habis");
                                }
                            };
                        }
                    ])
                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                        if (!$state) {
                            return;
                        }

                        $shippingDocument = $this->getOwnerRecord();

                        // Get invoice product details
                        $invoiceProduct = $shippingDocument->invoice->products()
                            ->where('product_id', $state)
                            ->first();

                        if (!$invoiceProduct) {
                            return;
                        }

                        // Set price from invoice
                        // $set('price', number_format($invoiceProduct->price, 0, ',', '.'));

                        // Calculate remaining quantity
                        $invoiceProductQuantity = $shippingDocument->invoice->products()
                            ->where('product_id', $state)
                            ->sum('quantity');

                        $usedQuantity = $shippingDocument->products()
                            ->where('product_id', $state)
                            ->sum('quantity');

                        $remainingQuantity = $invoiceProductQuantity - $usedQuantity;

                        // Set initial quantity to remaining stock
                        $set('quantity', max(0, $remainingQuantity));
                    }),
                // Forms\Components\TextInput::make('price')
                //     ->label(__('resources.shipping_document_product.price'))
                //     ->required()
                //     ->mask(RawJs::make('$money($input, \',\')'))
                //     ->stripCharacters('.')
                //     ->numeric()
                //     ->prefix('Rp'),
                Forms\Components\TextInput::make('quantity')
                    ->label(__('resources.shipping_document_product.quantity'))
                    ->required()
                    ->numeric()
                    ->rules([
                        function (Get $get, ?ShippingDocumentProduct $record = null) {
                            $product_id = $get('product_id');

                            return function (string $attribute, $value, \Closure $fail) use ($product_id, $record) {
                                $shippingDocument = $this->getOwnerRecord();

                                // Get total quantity from invoice
                                $invoiceQuantity = $shippingDocument->invoice->products()
                                    ->where('product_id', $product_id)
                                    ->sum('quantity');

                                // Get quantity already used in other shipping document records
                                $quantityInUse = $shippingDocument->products()
                                    ->where('product_id', $product_id)
                                    ->sum('quantity');

                                // When editing, exclude current record's quantity from used amount
                                if ($record && $record->product_id == $product_id) {
                                    $quantityInUse -= $record->quantity;
                                }

                                // Calculate how many items are still available
                                $availableQuantity = $invoiceQuantity - $quantityInUse;

                                // Check if requested quantity exceeds available amount
                                $quantityAfterRequest = $availableQuantity - $value;
                                if ($quantityAfterRequest < 0) {
                                    $fail("Jumlah barang ini melebihi sisa barang yang tersedia (" . max(0, $availableQuantity) . ")");
                                }
                            };
                        }
                    ]),
                Forms\Components\Select::make('status')
                    ->label(__('resources.shipping_document_product.status'))
                    ->options(ProductStatus::class)
                    ->enum(ProductStatus::class)
                    ->default(ProductStatus::PENDING)
                    ->required()
                    ->afterStateUpdated(function ($state, $record, $get) {
                        // For new records (no $record) that are set to done immediately
                        if ($state === 'done' && !$record) {
                            $productId = $get('product_id');
                            $quantity = $get('quantity');

                            // Increase product stock
                            Product::where('id', $productId)->increment('stock', $quantity);

                            // Stock log will be created after record is saved
                            return;
                        }

                        // For existing records being updated
                        if ($state === 'done' && $record && $record->status !== ProductStatus::DONE) {
                            // Increase product stock
                            Product::where('id', $record->product_id)->increment('stock', $record->quantity);

                            // Create stock log
                            ProductStockLog::create([
                                'product_id' => $record->product_id,
                                'quantity' => $record->quantity,
                                'type' => ProductStockStatus::IN,
                                'causer_type' => get_class($record),
                                'causer_id' => $record->id
                            ]);
                        }

                        if ($state !== 'done' && $record && $record->status === ProductStatus::DONE) {
                            // Decrease product stock
                            Product::where('id', $record->product_id)->decrement('stock', $record->quantity);

                            // Create stock log
                            ProductStockLog::create([
                                'product_id' => $record->product_id,
                                'quantity' => $record->quantity,
                                'type' => ProductStockStatus::OUT,
                                'causer_type' => get_class($record),
                                'causer_id' => $record->id
                            ]);
                        }
                    }),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('product.name')
            ->columns([
                Tables\Columns\TextColumn::make('product.name')
                    ->label(__('resources.shipping_document_product.product'))
                    ->formatStateUsing(fn ($record) => $record->product->code.' - '.$record->product->name)
                    ->searchable()
                    ->sortable(),
                // Tables\Columns\TextColumn::make('price')
                //     ->label(__('resources.shipping_document_product.price'))
                //     ->money('IDR')
                //     ->searchable()
                //     ->sortable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->label(__('resources.shipping_document_product.quantity'))
                    ->numeric()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('resources.shipping_document_product.status'))
                    ->badge()
                    ->color(fn (ProductStatus $state): string => match ($state) {
                        ProductStatus::CANCELED => 'danger',
                        ProductStatus::PENDING => 'warning',
                        ProductStatus::DONE => 'success',
                    })
                    ->formatStateUsing(fn (ProductStatus $state): string => $state->getLabel())
                    ->sortable(),
                Tables\Columns\TextColumn::make('status_at')
                    ->label(__('resources.shipping_document_product.status_at'))
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('resources.shipping_document_product.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('resources.shipping_document_product.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(function ($record) {
                        if ($record->status === ProductStatus::DONE) {
                            // Decrease product stock
                            Product::where('id', $record->product_id)->decrement('stock', $record->quantity);

                            // Create stock log
                            ProductStockLog::create([
                                'product_id' => $record->product_id,
                                'quantity' => $record->quantity,
                                'type' => ProductStockStatus::OUT,
                                'causer_type' => get_class($record),
                                'causer_id' => $record->id
                            ]);
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->before(function (Collection $records) {
                            foreach ($records as $record) {
                                if ($record->status === ProductStatus::DONE) {
                                    // Decrease product stock
                                    Product::where('id', $record->product_id)->decrement('stock', $record->quantity);

                                    // Create stock log
                                    ProductStockLog::create([
                                        'product_id' => $record->product_id,
                                        'quantity' => $record->quantity,
                                        'type' => ProductStockStatus::OUT,
                                        'causer_type' => get_class($record),
                                        'causer_id' => $record->id
                                    ]);
                                }
                            }
                        }),
                ]),
            ]);
    }

    protected function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
