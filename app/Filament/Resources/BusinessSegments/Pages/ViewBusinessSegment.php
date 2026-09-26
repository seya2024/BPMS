<?php

namespace App\Filament\Resources\BusinessSegments\Pages;

use App\Filament\Resources\BusinessSegments\BusinessSegmentResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewBusinessSegment extends ViewRecord
{
    protected static string $resource = BusinessSegmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
