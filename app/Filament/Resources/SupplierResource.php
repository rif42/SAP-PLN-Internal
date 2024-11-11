<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SupplierResource\Pages;
use App\Filament\Resources\SupplierResource\RelationManagers;
use App\Models\Supplier;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SupplierResource extends Resource
{
    protected static ?string $model = Supplier::class;
    protected static ?string $navigationIcon = 'heroicon-o-truck';
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?int $navigationSort = 30;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label(__('resources.supplier.name'))
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Section::make(__('resources.supplier.sales_contact'))
                    ->schema([
                        Forms\Components\TextInput::make('sales_name')
                            ->label(__('resources.supplier.sales_name'))
                            ->required(),
                        Forms\Components\TextInput::make('sales_phone')
                            ->label(__('resources.supplier.sales_phone'))
                            ->required()
                            ->tel(),
                        Forms\Components\TextInput::make('sales_email')
                            ->label(__('resources.supplier.sales_email'))
                            ->email(),
                    ])->columns(3),
                Forms\Components\Section::make(__('resources.supplier.logistics_contact'))
                    ->schema([
                        Forms\Components\TextInput::make('logistics_name')
                            ->label(__('resources.supplier.logistics_name'))
                            ->required(),
                        Forms\Components\TextInput::make('logistics_phone')
                            ->label(__('resources.supplier.logistics_phone'))
                            ->required()
                            ->tel(),
                        Forms\Components\TextInput::make('logistics_email')
                            ->label(__('resources.supplier.logistics_email'))
                            ->email(),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('resources.supplier.name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('sales_name')
                    ->label(__('resources.supplier.sales_name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('sales_phone')
                    ->label(__('resources.supplier.sales_phone'))
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('logistics_name')
                    ->label(__('resources.supplier.logistics_name'))
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('logistics_phone')
                    ->label(__('resources.supplier.logistics_phone'))
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('resources.supplier.created_at'))
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageSuppliers::route('/'),
        ];
    }

    public static function getModelLabel(): string 
    {
        return __('resources.supplier.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('resources.supplier.label');
    }

    public static function getBreadcrumb(): string
    {
        return __('resources.supplier.label');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
