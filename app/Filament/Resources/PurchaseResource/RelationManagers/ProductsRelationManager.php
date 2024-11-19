<?php

namespace App\Filament\Resources\PurchaseResource\RelationManagers;

use App\Enums\ProductStatus;
use App\Models\Product;
use App\Models\PurchaseProduct;
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
                    ->label(__('resources.purchase_product.product'))
                    ->options(function ($get) {
                        $purchase = $this->getOwnerRecord();
                        $procurementProducts = $purchase->procurement->products;

                        return $procurementProducts->mapToGroups(function ($procurementProduct) {
                            // Map each product to an array with its details
                            return [
                                $procurementProduct->product->id => [
                                    'id' => $procurementProduct->product->id,
                                    'label' => sprintf(
                                        '%s - %s',
                                        $procurementProduct->product->code,
                                        $procurementProduct->product->name
                                    ),
                                    'quantity' => $procurementProduct->quantity,
                                ],
                            ];
                        })->map(function ($items) use ($purchase) {
                            // Get the product label
                            $label = $items->first()['label'];
                            
                            // Calculate quantities
                            $totalQuantity = $items->sum('quantity');
                            $usedQuantity = $purchase->products()
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
                        function (Get $get, ?PurchaseProduct $record = null) {
                            return function (string $attribute, $value, \Closure $fail) use ($record) {
                                $purchase = $this->getOwnerRecord();
                                
                                // Get total quantity from procurement
                                $procurementProductQuantity = $purchase->procurement->products()
                                    ->where('product_id', $value)
                                    ->sum('quantity');

                                // Get used quantity from purchases
                                $usedQuantity = $purchase->products()
                                    ->where('product_id', $value)
                                    ->sum('quantity');

                                // Add back current quantity if editing existing record
                                if ($record && $record->product_id == $value) {
                                    $usedQuantity -= $record->quantity;
                                }

                                $remainingQuantity = $procurementProductQuantity - $usedQuantity;

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

                        $purchase = $this->getOwnerRecord();
                        
                        // Get procurement product details
                        $procurementProduct = $purchase->procurement->products()
                            ->where('product_id', $state)
                            ->first();

                        if (!$procurementProduct) {
                            return;
                        }
                        
                        // Set price from procurement
                        $set('price', number_format($procurementProduct->price, 0, ',', '.'));

                        // Calculate remaining quantity
                        $procurementProductQuantity = $purchase->procurement->products()
                            ->where('product_id', $state)
                            ->sum('quantity');

                        $usedQuantity = $purchase->products()
                            ->where('product_id', $state)
                            ->sum('quantity');

                        $remainingQuantity = $procurementProductQuantity - $usedQuantity;

                        // Set initial quantity to remaining stock
                        $set('quantity', max(0, $remainingQuantity));
                    }),
                Forms\Components\TextInput::make('price')
                    ->label(__('resources.purchase_product.price'))
                    ->required()
                    ->mask(RawJs::make('$money($input, \',\')'))
                    ->stripCharacters('.')
                    ->numeric()
                    ->prefix('Rp'),
                Forms\Components\TextInput::make('quantity')
                    ->label(__('resources.purchase_product.quantity'))
                    ->required()
                    ->numeric()
                    ->rules([
                        function (Get $get, ?PurchaseProduct $record = null) {
                            $product_id = $get('product_id');

                            return function (string $attribute, $value, \Closure $fail) use ($product_id, $record) {
                                $purchase = $this->getOwnerRecord();

                                // Get total quantity from procurement
                                $procurementQuantity = $purchase->procurement->products()
                                    ->where('product_id', $product_id)
                                    ->sum('quantity');

                                // Get quantity already used in other purchase records
                                $quantityInUse = $purchase->products()
                                    ->where('product_id', $product_id)
                                    ->sum('quantity');

                                // When editing, exclude current record's quantity from used amount
                                if ($record && $record->product_id == $product_id) {
                                    $quantityInUse -= $record->quantity;
                                }

                                // Calculate how many items are still available
                                $availableQuantity = $procurementQuantity - $quantityInUse;
                                
                                // Check if requested quantity exceeds available amount
                                $quantityAfterRequest = $availableQuantity - $value;
                                if ($quantityAfterRequest < 0) {
                                    $fail("Jumlah barang ini melebihi sisa barang yang tersedia (" . max(0, $availableQuantity) . ")");
                                }
                            };
                        }
                    ]),
                Forms\Components\Select::make('status')
                    ->label(__('resources.purchase_product.status'))
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
                    ->label(__('resources.purchase_product.product'))
                    ->formatStateUsing(fn ($record) => $record->product->code.' - '.$record->product->name)
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->label(__('resources.purchase_product.price'))
                    ->money('IDR')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->label(__('resources.purchase_product.quantity'))
                    ->numeric()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('resources.purchase_product.status'))
                    ->badge()
                    ->color(fn (ProductStatus $state): string => match ($state) {
                        ProductStatus::CANCELED => 'danger',
                        ProductStatus::PENDING => 'warning',
                        ProductStatus::DONE => 'success',
                    })
                    ->formatStateUsing(fn (ProductStatus $state): string => $state->getLabel())
                    ->sortable(),
                Tables\Columns\TextColumn::make('status_at')
                    ->label(__('resources.purchase_product.status_at'))
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('resources.purchase_product.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('resources.purchase_product.updated_at'))
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
