<?php

namespace App\Filament\Resources;

use App\Enums\ContractStatus;
use App\Filament\Resources\ContractResource\Pages;
use App\Filament\Resources\ContractResource\RelationManagers\ProductsRelationManager;
use App\Models\Contract;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ContractResource extends Resource
{
    protected static ?string $model = Contract::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $navigationGroup = 'Inventory';

    protected static ?int $navigationSort = 10;

    public static function getModelLabel(): string
    {
        return __('resources.contract.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('resources.contract.label');
    }

    public static function getBreadcrumb(): string
    {
        return __('resources.contract.label');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('procurement_id')
                    ->label(__('resources.contract.number'))
                    ->options(function () {
                        return \App\Models\Procurement::pluck('number', 'id');
                    })
                    ->searchable()
                    ->live()
                    ->afterStateUpdated(function ($state, \Filament\Forms\Set $set) {
                        if (!$state) return;

                        // Find the procurement
                        $procurement = \App\Models\Procurement::find($state);
                        if ($procurement) {
                            // Set the penugasan_id for display
                            $set('penugasan_id', $procurement->penugasan_id);

                            // Also set supplier from the procurement if it exists
                            if ($procurement->supplier_id) {
                                $set('supplier_id', $procurement->supplier_id);
                            }
                        } else {
                            // Clear fields if no procurement found
                            $set('penugasan_id', null);
                            $set('supplier_id', null);
                        }
                    })
                    ->afterStateHydrated(function ($state, $record, \Filament\Forms\Set $set) {
                        // When loading an existing record, set the penugasan_id field
                        if ($record && $record->procurement) {
                            $set('penugasan_id', $record->procurement->penugasan_id);
                        }
                    }),

                Forms\Components\TextInput::make('penugasan_id')
                    ->label(__('resources.contract.penugasan_id'))
                    ->disabled()
                    ->dehydrated(false),

                Forms\Components\Select::make('supplier_id')
                    ->label(__('resources.procurement.supplier'))
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
                Forms\Components\DateTimePicker::make('start_date')
                    ->label(__('resources.contract.start_date'))
                    ->required(),
                Forms\Components\DateTimePicker::make('end_date')
                    ->label(__('resources.contract.end_date'))
                    ->required(),
                Forms\Components\TextInput::make('total_amount')
                    ->label(__('resources.contract.total_amount'))
                    ->required()
                    ->mask(RawJs::make('$money($input, \',\')'))
                    ->stripCharacters('.')
                    ->numeric()
                    ->prefix('Rp'),
                Forms\Components\Select::make('status')
                    ->label(__('resources.contract.status'))
                    ->options(ContractStatus::class)
                    ->enum(ContractStatus::class)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                 Tables\Columns\TextColumn::make('status')
                    ->label(__('resources.contract.status'))
                    ->badge()
                    ->color(fn (ContractStatus $state): string => match ($state) {
                        ContractStatus::Pending => 'warning',
                        ContractStatus::Active => 'success',
                        ContractStatus::Canceled => 'danger',
                        ContractStatus::Done => 'info',
                        ContractStatus::Deal => 'primary',
                    })
                    ->formatStateUsing(fn (ContractStatus $state): string => $state->getLabel())
                    ->sortable(),

                Tables\Columns\TextColumn::make('procurement.number')
                    ->label(__('resources.contract.number'))
                    ->formatStateUsing(function ($record) {
                        return $record->procurement?->number ?? 'N/A';
                    })
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas('procurement', function ($query) use ($search) {
                            $query->where('number', 'like', "%{$search}%");
                        });
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('procurement.penugasan_id')
                    ->label(__('resources.contract.penugasan_id'))
                    ->formatStateUsing(function ($record) {
                        return $record->procurement?->penugasan_id ?? 'N/A';
                    })
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas('procurement', function ($query) use ($search) {
                            $query->where('penugasan_id', 'like', "%{$search}%");
                        });
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('supplier.name')
                    ->label(__('resources.contract.supplier'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->label(__('resources.contract.start_date'))
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->label(__('resources.contract.end_date'))
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_amount')
                    ->label(__('resources.contract.total_amount'))
                    ->money('IDR')
                    ->sortable(),


                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('resources.contract.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('resources.contract.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\Action::make('view_procurement')
                    ->label(__('resources.contract.view_procurement'))
                    ->icon('heroicon-o-document-text')
                    ->url(fn (Contract $record): string =>
                        route('filament.admin.resources.procurements.index', [
                            'tableFilters[contract][value]' => $record->id
                        ]))
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
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContracts::route('/'),
            'create' => Pages\CreateContract::route('/create'),
            'edit' => Pages\EditContract::route('/{record}/edit'),
        ];
    }

    public static function getNavigationLabel(): string
    {
        return __('resources.contract.label');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}






