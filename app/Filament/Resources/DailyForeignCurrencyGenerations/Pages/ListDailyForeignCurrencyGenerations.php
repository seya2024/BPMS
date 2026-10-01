<?php

namespace App\Filament\Resources\DailyForeignCurrencyGenerations\Pages;

use App\Filament\Resources\DailyForeignCurrencyGenerations\DailyForeignCurrencyGenerationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Carbon;

class ListDailyForeignCurrencyGenerations extends ListRecords
{
    protected static string $resource = DailyForeignCurrencyGenerationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->createAnother(true)
                ->label('Add Foreign Currency Generation')
                ->icon('heroicon-o-plus')
                ->outlined()
                ->size('sm'),

            Actions\Action::make('batchEntry')
                ->label('Batch Entry')
                ->icon('heroicon-o-square-3-stack-3d')
                ->color('gray')
                ->url(fn (): string => \App\Filament\Pages\BatchDailyForeignCurrencyGenerations::getUrl()),
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