<?php

namespace App\Filament\Resources\ShippingDocumentResource\RelationManagers;

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
                    ->label(__('resources.shipping_document_product.product'))
                    ->options(function ($get) {
                        $shippingDocument = $this->getOwnerRecord();
                        $invoiceProducts = $shippingDocument->invoice->products;

                        return $invoiceProducts->pluck('product')
                            ->mapWithKeys(function ($product) {
                                return [$product->id => $product->code.' - '.$product->name];
                            });
                    })
                    ->required()
                    ->live()
                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                        if ($state) {
                            $shippingDocument = $this->getOwnerRecord();
                            $invoiceProduct = $shippingDocument->invoice->products()
                                ->where('product_id', $state)
                                ->first();
                            if ($invoiceProduct) {
                                $set('price', number_format($invoiceProduct->price, 0, ',', '.'));
                            }
                        }
                    }),
                Forms\Components\TextInput::make('price')
                    ->label(__('resources.shipping_document_product.price'))
                    ->required()
                    ->mask(RawJs::make('$money($input, \',\')'))
                    ->stripCharacters('.')
                    ->numeric()
                    ->prefix('Rp'),
                Forms\Components\TextInput::make('quantity')
                    ->label(__('resources.shipping_document_product.quantity'))
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
                    ->label(__('resources.shipping_document_product.product'))
                    ->formatStateUsing(fn ($record) => $record->product->code.' - '.$record->product->name)
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->label(__('resources.shipping_document_product.price'))
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->label(__('resources.shipping_document_product.quantity'))
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
