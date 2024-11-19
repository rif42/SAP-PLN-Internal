<?php

namespace App\Filament\Resources\ProcurementResource\RelationManagers;

use App\Enums\ProductStatus;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
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
                Forms\Components\Select::make('status')
                    ->label(__('resources.procurement_product.status'))
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
                    ->label(__('resources.procurement_product.product'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->label(__('resources.procurement_product.price'))
                    ->money('IDR')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->label(__('resources.procurement_product.quantity'))
                    ->numeric()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('resources.procurement_product.status'))
                    ->badge()
                    ->color(fn (ProductStatus $state): string => match ($state) {
                        ProductStatus::CANCELED => 'danger',
                        ProductStatus::PENDING => 'warning',
                        ProductStatus::DONE => 'success',
                    })
                    ->formatStateUsing(fn (ProductStatus $state): string => $state->getLabel())
                    ->sortable(),
                Tables\Columns\TextColumn::make('status_at')
                    ->label(__('resources.procurement_product.status_at'))
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('resources.procurement_product.created_at'))
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('resources.procurement_product.updated_at'))
                    ->dateTime('d M Y H:i')
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
