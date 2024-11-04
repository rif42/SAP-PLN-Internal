<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StockResource\Pages;
use App\Filament\Resources\StockResource\RelationManagers;
use App\Models\Stock;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StockResource extends Resource
{
    protected static ?string $model = Stock::class;
    protected static ?string $navigationIcon = 'heroicon-o-archive-box';
    protected static ?string $navigationGroup = 'Inventory Management';
    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('product_id')
                    ->label(__('resources.stock.product'))
                    ->relationship('product', 'name')
                    ->required()
                    ->searchable()
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
                Forms\Components\TextInput::make('quantity')
                    ->label(__('resources.stock.quantity'))
                    ->required()
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('product.name')
                    ->label(__('resources.stock.product'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->label(__('resources.stock.quantity'))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('resources.stock.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                // ...
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStocks::route('/'),
            'create' => Pages\CreateStock::route('/create'),
            'edit' => Pages\EditStock::route('/{record}/edit'),
        ];
    }

    public static function getModelLabel(): string 
    {
        return __('resources.stock.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('resources.stock.label');
    }

    public static function getBreadcrumb(): string
    {
        return __('resources.stock.label');
    }

    public static function getNavigationLabel(): string
    {
        return __('resources.stock.label');
    }
}
