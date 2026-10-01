<?php

namespace App\Filament\Resources\DailySuperAppSubscriptions\Pages;

use App\Filament\Resources\DailySuperAppSubscriptions\DailySuperAppSubscriptionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Carbon;

class ListDailySuperAppSubscriptions extends ListRecords
{
    protected static string $resource = DailySuperAppSubscriptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->createAnother(true)
                ->label('Add Super App Subscription')
                ->icon('heroicon-o-plus')
                ->outlined()
                ->size('sm'),

            Actions\Action::make('batchEntry')
                ->label('Batch Entry')
                ->icon('heroicon-o-square-3-stack-3d')
                ->color('gray')
                ->url(fn (): string => \App\Filament\Pages\BatchDailySuperAppSubscriptions::getUrl()),
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