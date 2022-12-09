<?php

namespace App\Filament\Resources\EmployeesResource\Widgets;

use App\Models\country;
use App\Models\Employees;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use Illuminate\Support\Facades\DB;

class EmployeesStatsOverview extends BaseWidget
{
    protected function getCards(): array
    {
        $total = Employees::all()->count();
         $employees = Employees::select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(id) as total'))->groupBy('date')->get();
        // $lebels = [];
        // $employees_count_data = [];
         for ($i = 0; $i <= $total; $i++) {
            $date = now()->subDays($i);
        //     $lebels[] = $date->format('Y-m-d');
            $employees_count = collect($employees)->where('date', $date->format('Y-m-d'))->first();
           $employees_count_data = $employees_count ? $employees_count->total : 0;
         // $count=$employees_count->count();
         // dd( $employees_count);
        $kenya=country::where('name','kenya')->WithCount('employees')->first();
        $kenya2=Country::where('name','kenya2')->WithCount('employees')->first();
        return [
            Card::make('All Employees', Employees::all()->count())->description($employees_count_data.' Increase')->descriptionIcon('heroicon-s-trending-up')
            ->chart([7, 2, 10, 3])
            ->color('success'),
            Card::make('Kenya Employees', $kenya ? $kenya->employees_count:0)->description('increase')->descriptionIcon('heroicon-s-trending-up')->chart([2,8, 1,7])->color('danger'),
            Card::make('Kenya2 Employees', $kenya2 ? $kenya2->employees_count:0)->description('increase')->descriptionIcon('heroicon-s-trending-up')->chart([2,8, 1,7])->color('primary'),

        ];
    }
}
}
