<?php

namespace App\Filament\Resources;

use App\Enums\ProductStockStatus;
use App\Filament\Resources\ProductStockAdjustmentResource\Pages;
use App\Models\ProductStockAdjustment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProductStockAdjustmentResource extends Resource
{
    protected static ?string $model = ProductStockAdjustment::class;

    protected static ?string $navigationIcon = 'heroicon-o-inbox';

    protected static ?string $navigationGroup = 'Inventory';

    public static function getModelLabel(): string
    {
        return __('resources.product_stock_adjustment.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('resources.product_stock_adjustment.label');
    }

    public static function getBreadcrumb(): string
    {
        return __('resources.product_stock_adjustment.label');
    }

    public static function getNavigationLabel(): string
    {
        return __('resources.product_stock_adjustment.label');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('product_id')
                    ->label(__('resources.product_stock_adjustment.product'))
                    ->relationship('product', 'name')
                    ->required()
                    ->searchable(),
                Forms\Components\TextInput::make('quantity')
                    ->label(__('resources.product_stock_adjustment.quantity'))
                    ->required()
                    ->numeric(),
                Forms\Components\Select::make('type')
                    ->label(__('resources.product_stock_adjustment.type'))
                    ->options(ProductStockStatus::class)
                    ->enum(ProductStockStatus::class)
                    ->required(),
                Forms\Components\Textarea::make('reason')
                    ->label(__('resources.product_stock_adjustment.reason'))
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('product.name')
                    ->label(__('resources.product_stock_adjustment.product'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->label(__('resources.product_stock_adjustment.quantity'))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->label(__('resources.product_stock_adjustment.type'))
                    ->badge()
                    ->color(fn (ProductStockStatus $state): string => match ($state) {
                        ProductStockStatus::IN => 'success',
                        ProductStockStatus::OUT => 'danger',
                    })
                    ->formatStateUsing(fn (ProductStockStatus $state): string => $state->getLabel())
                    ->sortable(),
                Tables\Columns\TextColumn::make('reason')
                    ->label(__('resources.product_stock_adjustment.reason'))
                    ->wrap(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('resources.product_stock_adjustment.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('resources.product_stock_adjustment.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListProductStockAdjustments::route('/'),
            'create' => Pages\CreateProductStockAdjustment::route('/create'),
            'edit' => Pages\EditProductStockAdjustment::route('/{record}/edit'),
        ];
    }
}
