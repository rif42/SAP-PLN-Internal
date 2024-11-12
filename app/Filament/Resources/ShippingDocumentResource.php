<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ShippingDocumentResource\Pages;
use App\Filament\Resources\ShippingDocumentResource\RelationManagers\ProductsRelationManager;
use App\Models\ShippingDocument;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ShippingDocumentResource extends Resource
{
    protected static ?string $model = ShippingDocument::class;

    protected static ?string $navigationIcon = 'heroicon-o-document';

    protected static ?string $navigationGroup = 'Procurement';

    protected static ?int $navigationSort = 30;

    public static function getModelLabel(): string
    {
        return __('resources.shipping_document.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('resources.shipping_document.label');
    }

    public static function getBreadcrumb(): string
    {
        return __('resources.shipping_document.label');
    }

    public static function getNavigationLabel(): string
    {
        return __('resources.shipping_document.label');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('code')
                    ->label(__('resources.shipping_document.code'))
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->default(fn () => 'SHP-'.str_pad((ShippingDocument::withTrashed()->count() + 1), 5, '0', STR_PAD_LEFT))
                    ->readOnly(),
                Forms\Components\TextInput::make('number')
                    ->label(__('resources.shipping_document.number'))
                    ->required()
                    ->unique(ignoreRecord: true),
                Forms\Components\Select::make('procurement_id')
                    ->label(__('resources.shipping_document.procurement'))
                    ->relationship('procurement', 'code')
                    ->required()
                    ->searchable()
                    ->live(),
                Forms\Components\Select::make('invoice_id')
                    ->label(__('resources.shipping_document.invoice'))
                    ->relationship('invoice', 'code', function ($query, $get) {
                        $procurementId = $get('procurement_id');
                        if ($procurementId) {
                            return $query->where('procurement_id', $procurementId);
                        }

                        return $query->whereNull('id');
                    })
                    ->required()
                    ->searchable()
                    ->helperText(fn ($get) => ! filled($get('procurement_id')) ? 'Pilih procurement terlebih dahulu' : null),
                Forms\Components\Select::make('supplier_id')
                    ->label(__('resources.shipping_document.supplier'))
                    ->relationship('supplier', 'name')
                    ->required()
                    ->searchable()
                    ->createOptionForm([
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
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label(__('resources.shipping_document.code'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('number')
                    ->label(__('resources.shipping_document.number'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('invoice.code')
                    ->label(__('resources.shipping_document.invoice'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('supplier.name')
                    ->label(__('resources.shipping_document.supplier'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('procurement.code')
                    ->label(__('resources.shipping_document.procurement'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
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
            ProductsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListShippingDocuments::route('/'),
            'create' => Pages\CreateShippingDocument::route('/create'),
            'edit' => Pages\EditShippingDocument::route('/{record}/edit'),
        ];
    }
}
