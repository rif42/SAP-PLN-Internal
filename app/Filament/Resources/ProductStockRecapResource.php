<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductStockRecapResource\Pages;
use App\Filament\Resources\ProductStockRecapResource\RelationManagers;
use App\Models\ProductStockRecap;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;

class ProductStockRecapResource extends Resource
{
    protected static ?string $model = ProductStockRecap::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Report';

    public static function getModelLabel(): string
    {
        return __('resources.product_stock_recap.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('resources.product_stock_recap.label');
    }

    public static function getBreadcrumb(): string
    {
        return __('resources.product_stock_recap.label');
    }

    public static function getNavigationLabel(): string
    {
        return __('resources.product_stock_recap.label');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('date')
                    ->label(__('resources.product_stock_recap.date'))
                    ->required()
                    ->disabled(),
                Forms\Components\Select::make('product_id')
                    ->label(__('resources.product_stock_recap.product'))
                    ->relationship('product', 'name')
                    ->required()
                    ->disabled(),
                Forms\Components\TextInput::make('quantity')
                    ->label(__('resources.product_stock_recap.quantity'))
                    ->required()
                    ->numeric()
                    ->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('date', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('product.name')
                    ->label(__('resources.product_stock_recap.product'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->label(__('resources.product_stock_recap.quantity'))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('date')
                    ->label(__('resources.product_stock_recap.date'))
                    ->date('d M Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('product')
                    ->label(__('resources.product_stock_recap.product'))
                    ->relationship('product', 'name')
                    ->searchable()
                    ->preload(),
                DateRangeFilter::make('date')
                    ->label(__('resources.product_stock_recap.date'))
                    ->defaultThisMonth()
                    ->withIndicator()
                    ->timezone(config('app.timezone')),
            ])
            ->actions([])
            ->bulkActions([]);
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
            'index' => Pages\ManageProductStockRecaps::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
