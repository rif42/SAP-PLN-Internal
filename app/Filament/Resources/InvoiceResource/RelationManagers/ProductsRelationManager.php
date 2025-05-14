<?php

namespace App\Filament\Resources\InvoiceResource\RelationManagers;

use App\Enums\ProductStatus;
use App\Models\InvoiceProduct;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
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
                    ->label(__('resources.invoice_product.product'))
                    ->options(function ($get) {
                        $invoice = $this->getOwnerRecord();
                        $purchaseProducts = $invoice->purchase->products;

                        return $purchaseProducts->mapToGroups(function ($purchaseProduct) {
                            return [
                                $purchaseProduct->product->id => [
                                    'id' => $purchaseProduct->product->id,
                                    'label' => sprintf(
                                        '%s - %s',
                                        $purchaseProduct->product->code,
                                        $purchaseProduct->product->name
                                    ),
                                    'quantity' => $purchaseProduct->quantity,
                                ],
                            ];
                        })->map(function ($items) use ($invoice) {
                            // Get the product label
                            $label = $items->first()['label'];

                            // Calculate quantities
                            $totalQuantity = $items->sum('quantity');
                            $usedQuantity = $invoice->products()
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
                        function (Get $get, ?InvoiceProduct $record = null) {
                            return function (string $attribute, $value, \Closure $fail) use ($record) {
                                $invoice = $this->getOwnerRecord();

                                // Get total quantity from purchase
                                $purchaseProductQuantity = $invoice->purchase->products()
                                    ->where('product_id', $value)
                                    ->sum('quantity');

                                // Get used quantity from invoices
                                $usedQuantity = $invoice->products()
                                    ->where('product_id', $value)
                                    ->sum('quantity');

                                // Add back current quantity if editing existing record
                                if ($record && $record->product_id == $value) {
                                    $usedQuantity -= $record->quantity;
                                }

                                $remainingQuantity = $purchaseProductQuantity - $usedQuantity;

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

                        $invoice = $this->getOwnerRecord();

                        // Get purchase product details
                        $purchaseProduct = $invoice->purchase->products()
                            ->where('product_id', $state)
                            ->first();

                        if (!$purchaseProduct) {
                            return;
                        }

                        // Set price from purchase
                        // $set('price', number_format($purchaseProduct->price, 0, ',', '.'));

                        // Calculate remaining quantity
                        $purchaseProductQuantity = $invoice->purchase->products()
                            ->where('product_id', $state)
                            ->sum('quantity');

                        $usedQuantity = $invoice->products()
                            ->where('product_id', $state)
                            ->sum('quantity');

                        $remainingQuantity = $purchaseProductQuantity - $usedQuantity;

                        // Set initial quantity to remaining stock
                        $set('quantity', max(0, $remainingQuantity));
                    }),
                // Forms\Components\TextInput::make('price')
                //     ->label(__('resources.invoice_product.price'))
                //     ->required()
                //     ->mask(RawJs::make('$money($input, \',\')'))
                //     ->stripCharacters('.')
                //     ->numeric()
                //     ->prefix('Rp'),
                Forms\Components\TextInput::make('quantity')
                    ->label(__('resources.invoice_product.quantity'))
                    ->required()
                    ->numeric()
                    ->rules([
                        function (Get $get, ?InvoiceProduct $record = null) {
                            $product_id = $get('product_id');

                            return function (string $attribute, $value, \Closure $fail) use ($product_id, $record) {
                                $invoice = $this->getOwnerRecord();

                                // Get total quantity from purchase
                                $purchaseQuantity = $invoice->purchase->products()
                                    ->where('product_id', $product_id)
                                    ->sum('quantity');

                                // Get quantity already used in other invoice records
                                $quantityInUse = $invoice->products()
                                    ->where('product_id', $product_id)
                                    ->sum('quantity');

                                // When editing, exclude current record's quantity from used amount
                                if ($record && $record->product_id == $product_id) {
                                    $quantityInUse -= $record->quantity;
                                }

                                // Calculate how many items are still available
                                $availableQuantity = $purchaseQuantity - $quantityInUse;

                                // Check if requested quantity exceeds available amount
                                $quantityAfterRequest = $availableQuantity - $value;
                                if ($quantityAfterRequest < 0) {
                                    $fail("Jumlah barang ini melebihi sisa barang yang tersedia (" . max(0, $availableQuantity) . ")");
                                }
                            };
                        }
                    ]),
                Forms\Components\Select::make('status')
                    ->label(__('resources.invoice_product.status'))
                    ->options(ProductStatus::class)
                    ->enum(ProductStatus::class)
                    ->default(ProductStatus::PENDING)
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('product.name')
            ->columns([
                Tables\Columns\TextColumn::make('product.name')
                    ->label(__('resources.invoice_product.product'))
                    ->formatStateUsing(fn ($record) => $record->product->code.' - '.$record->product->name)
                    ->searchable()
                    ->sortable(),
                // Tables\Columns\TextColumn::make('price')
                //     ->label(__('resources.invoice_product.price'))
                //     ->money('IDR')
                //     ->searchable()
                //     ->sortable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->label(__('resources.invoice_product.quantity'))
                    ->numeric()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('resources.invoice_product.status'))
                    ->badge()
                    ->color(fn (ProductStatus $state): string => match ($state) {
                        ProductStatus::CANCELED => 'danger',
                        ProductStatus::PENDING => 'warning',
                        ProductStatus::DONE => 'success',
                    })
                    ->formatStateUsing(fn (ProductStatus $state): string => $state->getLabel())
                    ->sortable(),
                Tables\Columns\TextColumn::make('status_at')
                    ->label(__('resources.invoice_product.status_at'))
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('resources.invoice_product.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('resources.invoice_product.updated_at'))
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
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
