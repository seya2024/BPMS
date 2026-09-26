<?php

namespace App\Filament\Resources\BusinessSegments\Pages;

use App\Filament\Resources\BusinessSegments\BusinessSegmentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBusinessSegments extends ListRecords
{
    protected static string $resource = BusinessSegmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
           

               CreateAction::make()->createAnother(true)->label('Add Segment')
                ->createAnother(true)
                ->icon('heroicon-o-plus')
                 ->outlined()
                 ->size('sm')
        ];
    }
}
