<?php

namespace App\Filament\Resources\ContractResource\RelationManagers;

use App\Enums\ProductStatus;
use App\Enums\ProductStockStatus;
use App\Models\Product;
use App\Models\ProductStockLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
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
                    ->label(__('resources.contract_product.product'))
                    ->relationship('product', 'name')
                    ->required()
                    ->searchable()
                    ->live(),

                Forms\Components\TextInput::make('quantity')
                    ->label(__('resources.contract_product.quantity'))
                    ->required()
                    ->numeric(),
                Forms\Components\Select::make('status')
                    ->label(__('resources.contract_product.status'))
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
                    ->formatStateUsing(fn ($record) => $record->product->code.' - '.$record->product->name)
                    ->label(__('resources.contract_product.product'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->label(__('resources.contract_product.price'))
                    ->money('IDR')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->label(__('resources.contract_product.quantity'))
                    ->numeric()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('resources.contract_product.status'))
                    ->badge()
                    ->color(fn (ProductStatus $state): string => match ($state) {
                        ProductStatus::CANCELED => 'danger',
                        ProductStatus::PENDING => 'warning',
                        ProductStatus::DONE => 'success',
                    })
                    ->formatStateUsing(fn (ProductStatus $state): string => $state->getLabel())
                    ->sortable(),
                Tables\Columns\TextColumn::make('status_at')
                    ->label(__('resources.contract_product.status_at'))
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('resources.contract_product.created_at'))
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('resources.contract_product.updated_at'))
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->after(function (Model $record) {
                        // Decrement product stock
                        $product = Product::findOrFail($record->product_id);
                        $product->decrement('stock', $record->quantity);

                        // Create stock log
                        ProductStockLog::create([
                            'product_id' => $record->product_id,
                            'quantity' => $record->quantity,
                            'type' => ProductStockStatus::OUT,
                            'causer_type' => self::class,
                            'causer_id' => $record->id
                        ]);
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->before(function (Model $record, array $data) {
                        // Revert previous stock change
                        $product = Product::findOrFail($record->product_id);
                        $product->increment('stock', $record->quantity);

                        // Delete previous stock log
                        ProductStockLog::where('causer_type', self::class)
                            ->where('causer_id', $record->id)
                            ->delete();
                    })
                    ->after(function (Model $record) {
                        // Apply new stock change
                        $product = Product::findOrFail($record->product_id);
                        $product->decrement('stock', $record->quantity);

                        // Create new stock log
                        ProductStockLog::create([
                            'product_id' => $record->product_id,
                            'quantity' => $record->quantity,
                            'type' => 'OUT',
                            'causer_type' => self::class,
                            'causer_id' => $record->id
                        ]);
                    }),
                Tables\Actions\DeleteAction::make()
                    ->before(function (Model $record) {
                        // Revert stock change
                        $product = Product::findOrFail($record->product_id);
                        $product->increment('stock', $record->quantity);

                        // Delete stock log
                        ProductStockLog::where('causer_type', self::class)
                            ->where('causer_id', $record->id)
                            ->delete();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->before(function (Collection $records) {
                            foreach ($records as $record) {
                                // Revert stock change
                                $product = Product::findOrFail($record->product_id);
                                $product->increment('stock', $record->quantity);

                                // Delete stock log
                                ProductStockLog::where('causer_type', self::class)
                                    ->where('causer_id', $record->id)
                                    ->delete();
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


