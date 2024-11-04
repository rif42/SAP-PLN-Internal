<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\Transaction;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Products', Product::count()),
            Stat::make('Total Revenue', 'Rp ' . number_format(Purchase::sum('price'), 0, ',', '.')),
            Stat::make('Transaction In/Out', Transaction::where('transaction_type', 'in')->count() . ' / ' . Transaction::where('transaction_type', 'out')->count()),
        ];
    }
}
