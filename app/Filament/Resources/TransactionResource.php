<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Filament\Resources\TransactionResource\RelationManagers;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationGroup = 'Inventory Management';
    protected static ?int $navigationSort = 20;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('product_id')
                    ->label(__('resources.transaction.product'))
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
                Forms\Components\Radio::make('transaction_type')
                    ->label(__('resources.transaction.transaction_type'))
                    ->options([
                        'in' => 'In',
                        'out' => 'Out',
                    ])
                    ->required()
                    ->inline()
                    ->inlineLabel(false),
                Forms\Components\TextInput::make('quantity')
                    ->label(__('resources.transaction.quantity'))
                    ->required()
                    ->mask(RawJs::make('$money($input, \',\')'))
                    ->stripCharacters('.')
                    ->numeric(),
                Forms\Components\DateTimePicker::make('transaction_date')
                    ->label(__('resources.transaction.transaction_date'))
                    ->required()
                    ->default(now()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('product.name')
                    ->label(__('resources.transaction.product'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('transaction_type')
                    ->label(__('resources.transaction.transaction_type'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->label(__('resources.transaction.quantity'))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('transaction_date')
                    ->label(__('resources.transaction.transaction_date'))
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('resources.transaction.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('resources.transaction.updated_at'))
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
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransaction::route('/create'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
        ];
    }

    public static function getModelLabel(): string 
    {
        return __('resources.transaction.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('resources.transaction.label');
    }

    public static function getBreadcrumb(): string
    {
        return __('resources.transaction.label');
    }
}
