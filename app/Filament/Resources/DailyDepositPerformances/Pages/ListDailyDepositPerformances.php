<?php

namespace App\Filament\Resources\DailyDepositPerformances\Pages;

use App\Filament\Pages\BatchDailyDepositPerformances;
use App\Filament\Resources\DailyDepositPerformances\DailyDepositPerformanceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Carbon;

class ListDailyDepositPerformances extends ListRecords
{
    protected static string $resource = DailyDepositPerformanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->createAnother(true)
                ->label('Add Daily Deposit')
                ->icon('heroicon-o-plus')
                ->outlined()
                ->size('sm'),

            Actions\Action::make('batchEntry')
                ->label('Batch Entry')
                ->icon('heroicon-o-square-3-stack-3d')
                ->color('gray')
                ->url(fn (): string => BatchDailyDepositPerformances::getUrl()),
        ];
    }

    // Use query string to set default filter
    public function getDefaultTableFilters(): array
    {
        return [
            'business_day' => [
                'from' => Carbon::yesterday()->toDateString(),
                'until' => Carbon::yesterday()->toDateString(),
            ],
        ];
    }
}