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
        ];
    }
}
