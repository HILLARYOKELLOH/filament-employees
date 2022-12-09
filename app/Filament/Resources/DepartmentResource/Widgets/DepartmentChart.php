<?php

namespace App\Filament\Resources\DepartmentResource\Widgets;

use App\Models\Department;
use Filament\Widgets\BarChartWidget;
use Illuminate\Support\Facades\DB;

class DepartmentChart extends BarChartWidget
{
    protected static ?string $maxHeight = '500px';
    protected static ?string $heading = 'Bar Graph';

    protected function getData(): array

    {
        $total = Department::all()->count();
        $department = Department::select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(id) as total'))->groupBy('date')->get();
        $lebels = [];
        $data = [];
        for ($i = 0; $i <= $total; $i++) {
            $date = now()->subDays($i);
            $lebels[] = $date->format('Y-m-d');
            $department_count = collect($department)->where('date', $date->format('Y-m-d'))->first();
            $data[] = $department_count ? $department_count->total : 0;



       }


        return [
            'datasets' => [
                [
                    'label' => 'Total Departments Created',
                    'data' => $data,
                ],
            ],
            'labels' =>$lebels,

        ];
    }
}
