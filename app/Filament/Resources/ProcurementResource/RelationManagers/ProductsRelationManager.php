<?php

namespace App\Filament\Resources\ProcurementResource\RelationManagers;

use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Table;

class ProductsRelationManager extends RelationManager
{
    protected static string $relationship = 'products';

    protected static ?string $title = 'Barang';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('product_id')
                    ->label(__('resources.procurement_product.product'))
                    ->relationship('product', 'name')
                    ->required()
                    ->searchable()
                    ->live()
                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                        if ($state) {
                            $product = Product::find($state);
                            if ($product) {
                                $set('price', number_format($product->price, 0, ',', '.'));
                            }
                        }
                    })
                    ->createOptionForm([
                        Forms\Components\TextInput::make('name')
                            ->label(__('resources.product.name'))
                            ->required(),
                        Forms\Components\Select::make('category_id')
                            ->label(__('resources.product.category'))
                            ->relationship('category', 'name')
                            ->required()
                            ->searchable(),
                        Forms\Components\TextInput::make('barcode')
                            ->label(__('resources.product.barcode'))
                            ->required()
                            ->numeric(),
                        Forms\Components\TextInput::make('price')
                            ->label(__('resources.product.price'))
                            ->required()
                            ->mask(RawJs::make('$money($input, \',\')'))
                            ->stripCharacters('.')
                            ->numeric()
                            ->prefix('Rp'),
                        Forms\Components\Textarea::make('description')
                            ->label(__('resources.product.description'))
                            ->required(),
                    ]),
                Forms\Components\TextInput::make('price')
                    ->label(__('resources.procurement_product.price'))
                    ->required()
                    ->mask(RawJs::make('$money($input, \',\')'))
                    ->stripCharacters('.')
                    ->numeric()
                    ->prefix('Rp'),
                Forms\Components\TextInput::make('quantity')
                    ->label(__('resources.procurement_product.quantity'))
                    ->required()
                    ->numeric(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('product.name')
            ->columns([
                Tables\Columns\TextColumn::make('product.name')
                    ->label(__('resources.procurement_product.product'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->label(__('resources.procurement_product.price'))
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->label(__('resources.procurement_product.quantity'))
                    ->numeric()
                    ->sortable(),
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
}
