<?php

namespace App\Filament\Resources;

use App\Enums\ProductStockStatus;
use App\Filament\Resources\MovementResource\Pages;
use App\Models\Movement;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MovementResource extends Resource
{
    protected static ?string $model = Movement::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path';

    protected static ?string $navigationGroup = 'Inventory';

    protected static ?int $navigationSort = 20;

    public static function getModelLabel(): string
    {
        return __('resources.movement.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('resources.movement.label');
    }

    public static function getBreadcrumb(): string
    {
        return __('resources.movement.label');
    }

    public static function getNavigationLabel(): string
    {
        return __('resources.movement.label');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('date')
                    ->label(__('resources.movement.date'))
                    ->required(),
                Forms\Components\Select::make('product_id')
                    ->label(__('resources.movement.product'))
                    ->relationship('product', 'name')
                    ->required()
                    ->searchable(),
                Forms\Components\Select::make('type')
                    ->label(__('resources.movement.type'))
                    ->options(ProductStockStatus::class)
                    ->enum(ProductStockStatus::class)
                    ->default(ProductStockStatus::IN)
                    ->required(),
                Forms\Components\TextInput::make('quantity')
                    ->label(__('resources.movement.quantity'))
                    ->required()
                    ->numeric()
                    ->integer(),
                Forms\Components\Textarea::make('description')
                    ->label(__('resources.movement.description'))
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->label(__('resources.movement.date'))
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('product.name')
                    ->label(__('resources.movement.product'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->label(__('resources.movement.type'))
                    ->badge()
                    ->color(fn (ProductStockStatus $state): string => match ($state) {
                        ProductStockStatus::IN => 'success',
                        ProductStockStatus::OUT => 'danger',
                    })
                    ->formatStateUsing(fn (ProductStockStatus $state): string => $state->getLabel())
                    ->sortable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->label(__('resources.movement.quantity'))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->label(__('resources.movement.description'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
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
            'index' => Pages\ListMovements::route('/'),
            'create' => Pages\CreateMovement::route('/create'),
            'edit' => Pages\EditMovement::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
