<?php

namespace App\Filament\Resources;

use App\Enums\ProductStatus;
use App\Filament\Resources\InvoiceResource\Pages;
use App\Filament\Resources\InvoiceResource\RelationManagers\ProductsRelationManager;
use App\Filament\Resources\InvoiceResource\RelationManagers\ShippingDocumentsRelationManager;
use App\Models\Invoice;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Procurement';

    protected static ?int $navigationSort = 30;

    public static function getModelLabel(): string
    {
        return __('resources.invoice.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('resources.invoice.label');
    }

    public static function getBreadcrumb(): string
    {
        return __('resources.invoice.label');
    }

    public static function getNavigationLabel(): string
    {
        return __('resources.invoice.label');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('code')
                    ->label(__('resources.invoice.code'))
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->default(fn () => 'INV-'.str_pad((Invoice::withTrashed()->count() + 1), 5, '0', STR_PAD_LEFT))
                    ->readOnly(),

                Forms\Components\Select::make('number')
                    ->label(__('resources.invoice.number'))
                    ->options(function () {
                        return \App\Models\Purchase::with('procurement')
                            ->get()
                            ->mapWithKeys(function ($purchase) {
                                return [
                                    $purchase->id => $purchase->procurement?->number ?? 'Tanpa Nomor',
                                ];
                            });
                    })
                    ->searchable()
                    ->live()
                    ->afterStateUpdated(function ($state, \Filament\Forms\Set $set) {
                        $purchase = \App\Models\Purchase::with('procurement')->find($state);
                        if ($purchase) {
                            // Set the actual purchase_id for database storage
                            $set('purchase_id', $state);

                            // Set a display field for showing the purchase code to the user
                            $set('purchase_code', $purchase->code);
                        }
                    })
                    ->afterStateHydrated(function ($state, $record, \Filament\Forms\Set $set) {
                        // When loading an existing record, set the purchase_code field
                        if ($record && $record->purchase) {
                            $set('purchase_code', $record->purchase->code);

                            // Also set the number field to the purchase_id for the dropdown
                            $set('number', $record->purchase_id);
                        }
                    }),

                // Hidden field to store the actual purchase_id for the database relationship
                Forms\Components\Hidden::make('purchase_id')
                    ->required(),

                // Display-only field to show the purchase code to the user
                Forms\Components\TextInput::make('purchase_code')
                    ->label(__('resources.invoice.purchase'))
                    ->disabled()
                    ->dehydrated(false),


                Forms\Components\Select::make('supplier_id')
                    ->label(__('resources.invoice.supplier'))
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
                Forms\Components\DatePicker::make('date')
                    ->label(__('resources.invoice.date'))
                    ->default(now())
                    ->required(),
                Forms\Components\Select::make('status')
                    ->label(__('resources.invoice.status'))
                    ->options(ProductStatus::class)
                    ->enum(ProductStatus::class)
                    ->default(ProductStatus::PENDING)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label(__('resources.invoice.code'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('resources.invoice.status'))
                    ->badge()
                    ->color(fn (ProductStatus $state): string => match ($state) {
                        ProductStatus::CANCELED => 'danger',
                        ProductStatus::PENDING => 'warning',
                        ProductStatus::DONE => 'success',
                    })
                    ->formatStateUsing(fn (ProductStatus $state): string => $state->getLabel())
                    ->sortable(),
                Tables\Columns\TextColumn::make('number')
                    ->label(__('resources.invoice.number'))
                    ->formatStateUsing(function ($record) {
                        // Get the procurement number through the purchase relationship
                        return $record->purchase?->procurement?->number ?? 'N/A';
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('date')
                    ->label(__('resources.invoice.date'))
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('purchase.code') // Changed from purchase_id to purchase.code
                    ->label(__('resources.invoice.purchase'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('supplier.name')
                    ->label(__('resources.invoice.supplier'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('status_at')
                    ->label(__('resources.invoice.status_at'))
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('resources.invoice.created_at'))
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('resources.invoice.updated_at'))
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->label(__('resources.invoice.deleted_at'))
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('purchase')
                    ->label(__('resources.invoice.purchase'))
                    ->relationship('purchase', 'code')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('supplier')
                    ->label(__('resources.invoice.supplier'))
                    ->relationship('supplier', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\Action::make('view_shipping_documents')
                    ->label(__('resources.invoice.view_shipping_documents'))
                    ->icon('heroicon-o-document')
                    ->url(fn (Invoice $record): string =>
                        ShippingDocumentResource::getUrl('index', ['tableFilters[invoice][value]' => $record->id]))
                    ->openUrlInNewTab(),
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
            ProductsRelationManager::class,
            ShippingDocumentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
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







