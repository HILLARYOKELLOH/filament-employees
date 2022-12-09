<?php

namespace App\Filament\Resources\EmployeesResource\Widgets;

use App\Models\Employees;
use Filament\Widgets\LineChartWidget;
use Illuminate\Support\Facades\DB;

class EmployeesChart extends LineChartWidget
{
    protected static ?string $heading = 'Line Graph';
    protected static ?string $maxHeight = '500px';


    protected function getData(): array
    {
        $total = Employees::all()->count();
        $employees = Employees::select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(id) as total'))->groupBy('date')->get();
        $lebels = [];
        $employees_count_data = [];
        for ($i = 0; $i <= $total; $i++) {
            $date = now()->subDays($i);
            $lebels[] = $date->format('Y-m-d');
            $employees_count = collect($employees)->where('date', $date->format('Y-m-d'))->first();
            $employees_count_data[] = $employees_count ? $employees_count->total : 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Employees Line Graph',
                    'data' => $employees_count_data,
                ],
            ],
            'labels' => $lebels,
        ];
    }
}
