<?php

namespace App\Filament\Resources\DailyAccountOpenings\Pages;

use App\Filament\Pages\BatchDailyAccountOpenings;
use App\Filament\Resources\DailyAccountOpenings\DailyAccountOpeningResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDailyAccountOpenings extends ListRecords
{
    protected static string $resource = DailyAccountOpeningResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),

            Actions\Action::make('batchEntry')
                ->label('Batch Entry')
                ->icon('heroicon-o-square-3-stack-3d')
                ->color('gray')
                ->url(fn (): string => BatchDailyAccountOpenings::getUrl(panel: 'admin')),
        ];
    }
}
