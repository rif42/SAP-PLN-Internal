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

                        // Existing document fields...
                    ]),

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
                Forms\Components\Select::make('invoice_id')
                    ->label(__('resources.shipping_document.invoice'))
                    ->relationship('invoice', 'code', fn (Builder $query) => $query->selectRaw("id, code || ' - ' || number as code"))
                    ->required()
                    ->searchable()
                    ->live()
                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                        if ($state) {
                            $invoice = \App\Models\Invoice::find($state);
                            if ($invoice) {
                                $set('supplier_id', $invoice->supplier_id);
                            }
                        }
                    }),
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
                    ->searchable(),
                Tables\Columns\TextColumn::make('invoice.code')
                    ->label(__('resources.shipping_document.invoice'))
                    ->searchable(),
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
