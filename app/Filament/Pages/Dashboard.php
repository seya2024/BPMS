<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    public function getWidgets(): array
    {
        return [
            \App\Filament\Widgets\PerformanceOverviewWidget::class,
            \App\Filament\Widgets\DepositPerformanceChart::class,
            \App\Filament\Widgets\AccountPerformanceChart::class,
            \App\Filament\Widgets\BranchPerformanceTable::class,
            \App\Filament\Widgets\DistrictPerformanceChart::class,
        ];
    }
}
