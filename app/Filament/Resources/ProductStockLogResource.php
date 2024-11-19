<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductStockLogResource\Pages;
use App\Filament\Resources\ProductStockLogResource\RelationManagers;
use App\Models\ProductStockLog;
use App\Enums\ProductStockStatus;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;

class ProductStockLogResource extends Resource
{
    protected static ?string $model = ProductStockLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path-rounded-square';

    protected static ?string $navigationGroup = 'Report';

    public static function getModelLabel(): string
    {
        return __('resources.product_stock_log.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('resources.product_stock_log.label');
    }

    public static function getBreadcrumb(): string
    {
        return __('resources.product_stock_log.label');
    }

    public static function getNavigationLabel(): string
    {
        return __('resources.product_stock_log.label');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('product_id')
                    ->label(__('resources.product_stock_log.product'))
                    ->relationship('product', 'name')
                    ->required()
                    ->disabled(),
                Forms\Components\TextInput::make('quantity')
                    ->label(__('resources.product_stock_log.quantity'))
                    ->required()
                    ->numeric()
                    ->disabled(),
                Forms\Components\TextInput::make('type')
                    ->label(__('resources.product_stock_log.type'))
                    ->required()
                    ->disabled(),
                Forms\Components\TextInput::make('causer_type')
                    ->label(__('resources.product_stock_log.causer_type'))
                    ->disabled(),
                Forms\Components\TextInput::make('causer_id')
                    ->label(__('resources.product_stock_log.causer_id'))
                    ->numeric()
                    ->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('product.name')
                    ->label(__('resources.product_stock_log.product'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->label(__('resources.product_stock_log.quantity'))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->label(__('resources.product_stock_log.type'))
                    ->badge()
                    ->color(fn (ProductStockStatus $state): string => match ($state) {
                        ProductStockStatus::IN => 'success',
                        ProductStockStatus::OUT => 'danger',
                    })
                    ->formatStateUsing(fn (ProductStockStatus $state): string => $state->getLabel())
                    ->sortable(),
                Tables\Columns\TextColumn::make('causer_id')
                    ->label(__('resources.product_stock_log.causer'))
                    ->numeric()
                    ->sortable()
                    ->formatStateUsing(fn ($record) => class_basename($record->causer_type) . ' #' . $record->causer_id),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('resources.product_stock_log.created_at'))
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('product')
                    ->label(__('resources.product_stock_log.product'))
                    ->relationship('product', 'name')
                    ->searchable()
                    ->preload(),
                DateRangeFilter::make('created_at')
                    ->label(__('resources.product_stock_log.created_at'))
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
            'index' => Pages\ManageProductStockLogs::route('/'),
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
