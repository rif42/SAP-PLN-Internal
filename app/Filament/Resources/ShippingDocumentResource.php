<?php

namespace App\Filament\Resources;

use App\Enums\ProductStatus;
use App\Filament\Resources\ShippingDocumentResource\Pages;
use App\Filament\Resources\ShippingDocumentResource\RelationManagers\ProductsRelationManager;
use App\Models\ShippingDocument;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Illuminate\Support\Facades\Storage;


class ShippingDocumentResource extends Resource
{
    protected static ?string $model = ShippingDocument::class;

    protected static ?string $navigationIcon = 'heroicon-o-document';

    protected static ?string $navigationGroup = 'Procurement';

    protected static ?int $navigationSort = 40;

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
                Forms\Components\Section::make(__('resources.procurement.documents'))
                    ->schema([
                        Forms\Components\FileUpload::make('suratJalan_document')
                            ->label(__('Input File Surat Jalan'))
                            ->helperText('Upload dokumen Surat Jalan dalam format PDF')
                            ->acceptedFileTypes(['application/pdf'])
                            ->disk('public') // Explicitly set the disk to public
                            ->directory('procurement-suratJalan-documents')
                            ->maxSize(10240) // 10MB
                            ->downloadable()
                            ->openable()
                            ->previewable(true)
                            // Custom file naming based on procurement code
                            ->getUploadedFileNameForStorageUsing(
                                function (TemporaryUploadedFile $file, callable $get) {
                                    $code = $get('code');
                                    return "Surat-Jalan_{$code}.pdf";
                                }
                            )
                            ->visibility('public')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\TextInput::make('code')
                    ->label(__('resources.shipping_document.code'))
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->default(fn () => 'SHP-'.str_pad((ShippingDocument::withTrashed()->count() + 1), 5, '0', STR_PAD_LEFT))
                    ->readOnly(),

                // Updated Select for procurement numbers
                Forms\Components\Select::make('number')
                    ->label(__('resources.shipping_document.number'))
                    ->options(function () {
                        return \App\Models\Procurement::pluck('number', 'id');
                    })
                    ->searchable()
                    ->live()
                    ->afterStateUpdated(function ($state, \Filament\Forms\Set $set) {
                        if (!$state) return;

                        // Find invoice related to this procurement
                        $invoice = \App\Models\Invoice::where('purchase_id', function ($query) use ($state) {
                            $query->select('id')
                                ->from('purchases')
                                ->where('number', $state);
                        })->first();

                        if ($invoice) {
                            // Set the invoice_id for database storage
                            $set('invoice_id', $invoice->id);

                            // Set the invoice_code for display
                            $set('invoice_code', $invoice->code);

                            // Also set supplier from the invoice
                            $set('supplier_id', $invoice->supplier_id);
                        } else {
                            // Clear fields if no invoice found
                            $set('invoice_id', null);
                            $set('invoice_code', null);
                            $set('supplier_id', null);
                        }
                    })
                    ->afterStateHydrated(function ($state, $record, \Filament\Forms\Set $set) {
                        // When loading an existing record, set the fields correctly
                        if ($record && $record->invoice) {
                            // Get the procurement ID from the invoice's purchase
                            $purchase = $record->invoice->purchase;
                            if ($purchase) {
                                $set('number', $purchase->number);
                            }

                            $set('invoice_code', $record->invoice->code);
                            $set('invoice_id', $record->invoice_id);
                        }
                    }),

                Forms\Components\Hidden::make('invoice_id')
                    ->required(),

                Forms\Components\TextInput::make('invoice_code')
                    ->label(__('resources.shipping_document.invoice'))
                    ->disabled()
                    ->dehydrated(false),

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
                Forms\Components\Select::make('status')
                    ->label(__('resources.shipping_document.status'))
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
                    ->label(__('resources.shipping_document.code'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('number')
                    ->label(__('resources.shipping_document.number'))
                    ->formatStateUsing(function ($record) {
                        // Get the procurement number through the invoice->purchase relationship
                        return $record->invoice?->purchase?->procurement?->number ?? 'N/A';
                    })
                    ->searchable(),

                Tables\Columns\TextColumn::make('invoice.code')
                    ->label(__('resources.shipping_document.invoice'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('supplier.name')
                    ->label(__('resources.shipping_document.supplier'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('resources.shipping_document.status'))
                    ->badge()
                    ->color(fn (ProductStatus $state): string => match ($state) {
                        ProductStatus::CANCELED => 'danger',
                        ProductStatus::PENDING => 'warning',
                        ProductStatus::DONE => 'success',
                    })
                    ->formatStateUsing(fn (ProductStatus $state): string => $state->getLabel())
                    ->sortable(),
                Tables\Columns\TextColumn::make('status_at')
                    ->label(__('resources.shipping_document.status_at'))
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('resources.shipping_document.created_at'))
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('resources.shipping_document.updated_at'))
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->label(__('resources.shipping_document.deleted_at'))
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('suratJalan_document')
                    ->label('File Surat Jalan')
                    ->formatStateUsing(function ($state, $record) {
                        if (empty($state)) {
                            return '-';
                        }

                        return "Surat-Jalan_{$record->code}.pdf";
                    })
                    ->icon('heroicon-o-document-text')
                    ->color('success')
                    ->url(fn ($record) => $record->suratJalan_document ? Storage::disk('public')->url($record->suratJalan_document) : null)
                    ->openUrlInNewTab()
                    ->searchable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('invoice')
                    ->label(__('resources.invoice.invoice'))
                    ->relationship('invoice', 'code')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('supplier')
                    ->label(__('resources.shipping_document.supplier'))
                    ->relationship('supplier', 'name')
                    ->searchable()
                    ->preload(),
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

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}




