<?php

namespace App\Filament\Resources\InvoiceResource\RelationManagers;

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
                    ->label(__('resources.invoice_product.product'))
                    ->options(function ($get) {
                        $invoice = $this->getOwnerRecord();
                        $procurementProducts = $invoice->procurement->products;

                        return $procurementProducts->pluck('product')
                            ->mapWithKeys(function ($product) {
                                return [$product->id => $product->code.' - '.$product->name];
                            });
                    })
                    ->required()
                    ->live()
                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                        if ($state) {
                            $invoice = $this->getOwnerRecord();
                            $procurementProduct = $invoice->procurement->products()
                                ->where('product_id', $state)
                                ->first();
                            if ($procurementProduct) {
                                $set('price', number_format($procurementProduct->price, 0, ',', '.'));
                            }
                        }
                    }),
                Forms\Components\TextInput::make('price')
                    ->label(__('resources.invoice_product.price'))
                    ->required()
                    ->mask(RawJs::make('$money($input, \',\')'))
                    ->stripCharacters('.')
                    ->numeric()
                    ->prefix('Rp'),
                Forms\Components\TextInput::make('quantity')
                    ->label(__('resources.invoice_product.quantity'))
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
                    ->label(__('resources.invoice_product.product'))
                    ->formatStateUsing(fn ($record) => $record->product->code.' - '.$record->product->name)
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->label(__('resources.invoice_product.price'))
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->label(__('resources.invoice_product.quantity'))
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
