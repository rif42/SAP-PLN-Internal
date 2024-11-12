<?php

namespace App\Filament\Widgets;

use App\Models\Contract;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class TotalAmountContractPerSupplierChart extends ChartWidget
{
    protected static ?string $heading = 'Total Kontrak per Pemasok';

    public ?string $filter = 'year';

    protected static string $color = 'success';

    protected int|string|array $columnSpan = 'full';

    protected function getFilters(): ?array
    {
        return [
            'today' => 'Hari Ini',
            'week' => 'Minggu Ini',
            'month' => 'Bulan Ini',
            'year' => 'Tahun Ini',
            'all' => 'Semua',
        ];
    }

    protected function getData(): array
    {
        $query = Contract::query()
            ->select([
                'suppliers.name',
                DB::raw('SUM(contracts.total_amount) as total'),
            ])
            ->join('suppliers', 'suppliers.id', '=', 'contracts.supplier_id')
            ->groupBy('suppliers.id', 'suppliers.name');

        $query = match ($this->filter) {
            'today' => $query->whereDate('contracts.created_at', now()->today()),
            'week' => $query->whereBetween('contracts.created_at', [now()->startOfWeek(), now()->endOfWeek()]),
            'month' => $query->whereMonth('contracts.created_at', now()->month),
            'year' => $query->whereYear('contracts.created_at', now()->year),
            default => $query
        };

        $data = $query->get();

        return [
            'datasets' => [
                [
                    'label' => 'Total Kontrak',
                    'data' => $data->pluck('total')->toArray(),
                    'backgroundColor' => [
                        '#36A2EB',
                        '#FF6384',
                        '#4BC0C0',
                        '#FF9F40',
                        '#9966FF',
                    ],
                ],
            ],
            'labels' => $data->pluck('name')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
