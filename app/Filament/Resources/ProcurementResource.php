<?php

namespace App\Filament\Resources;

use App\Enums\ProductStatus;
use App\Filament\Resources\ProcurementResource\Pages;
use App\Filament\Resources\ProcurementResource\RelationManagers\InvoicesRelationManager;
use App\Filament\Resources\ProcurementResource\RelationManagers\ProductsRelationManager;
use App\Filament\Resources\ProcurementResource\RelationManagers\PurchasesRelationManager;
use App\Models\Procurement;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Contract;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;

class ProcurementResource extends Resource
{
    protected static ?string $model = Procurement::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard';

    protected static ?string $navigationGroup = 'Procurement';

    public static function getModelLabel(): string
    {
        return __('resources.procurement.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('resources.procurement.label');
    }

    public static function getBreadcrumb(): string
    {
        return __('resources.procurement.label');
    }

    public static function getNavigationLabel(): string
    {
        return __('resources.procurement.label');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('code')
                    ->label(__('resources.procurement.code'))
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->default(fn () => 'PRC-'.str_pad((Procurement::withTrashed()->count() + 1), 5, '0', STR_PAD_LEFT))
                    ->readOnly(),
                Forms\Components\TextInput::make('number')
                    ->label(__('resources.procurement.number'))
                    ->required()
                    ->unique(ignoreRecord: true),
                Forms\Components\Select::make('contract_id')
                    ->label(__('resources.procurement.contract'))
                    ->relationship(
                        name: 'contract',
                        modifyQueryUsing: fn (Builder $query) => $query
                            ->join('suppliers', 'contracts.supplier_id', '=', 'suppliers.id')
                            ->select(
                                'contracts.id', 
                                'suppliers.name as supplier_name', 
                                'contracts.start_date', 
                                'contracts.end_date'
                            )
                    )
                    ->getOptionLabelFromRecordUsing(function (Model $record) {
                        $startDate = Carbon::parse($record->start_date)->format('d M Y');
                        $endDate = Carbon::parse($record->end_date)->format('d M Y');
                        
                        return "{$record->supplier_name} ({$startDate} - {$endDate})";
                    })
                    ->required()
                    ->searchable(
                        ['supplier_name', 'contracts.start_date', 'contracts.end_date', 'contracts.total_amount']
                    ) 
                    ->live(),
                Forms\Components\DatePicker::make('start_date')
                    ->label(__('resources.procurement.start_date'))
                    ->required(),
                Forms\Components\DatePicker::make('end_date')
                    ->label(__('resources.procurement.end_date'))
                    ->required(),
                Forms\Components\Select::make('status')
                    ->label(__('resources.procurement.status'))
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
                    ->label(__('resources.procurement.code'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('number')
                    ->label(__('resources.procurement.number'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('contract.id')
                    ->label(__('resources.procurement.contract'))
                    ->formatStateUsing(function ($state, Procurement $record) {
                        $contract = $record->contract; // Access the related contract model
                        if (!$contract) {
                            return '-';
                        }
                        $supplierName = $contract->supplier?->name ?? 'N/A';
                        $startDate = $contract->start_date?->format('d M Y') ?? 'N/A';
                        $endDate = $contract->end_date?->format('d M Y') ?? 'N/A';

                        return "{$supplierName} ({$startDate} - {$endDate})";
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->label(__('resources.procurement.start_date'))
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->label(__('resources.procurement.end_date'))
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('resources.procurement.status'))
                    ->badge()
                    ->color(fn (ProductStatus $state): string => match ($state) {
                        ProductStatus::CANCELED => 'danger',
                        ProductStatus::PENDING => 'warning',
                        ProductStatus::DONE => 'success',
                    })
                    ->formatStateUsing(fn (ProductStatus $state): string => $state->getLabel())
                    ->sortable(),
                Tables\Columns\TextColumn::make('status_at')
                    ->label(__('resources.procurement.status_at'))
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('resources.procurement.created_at'))
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('resources.procurement.updated_at'))
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->label(__('resources.procurement.deleted_at'))
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('supplier')
                    ->label(__('resources.procurement.supplier'))
                    ->relationship('contract.supplier', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\Action::make('view_purchases')
                    ->label(__('resources.procurement.view_purchases'))
                    ->icon('heroicon-o-shopping-cart')
                    ->url(fn (Procurement $record): string => 
                        PurchaseResource::getUrl('index', ['tableFilters[procurement][value]' => $record->id]))
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
            PurchasesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProcurements::route('/'),
            'create' => Pages\CreateProcurement::route('/create'),
            'edit' => Pages\EditProcurement::route('/{record}/edit'),
        ];
    }
}
