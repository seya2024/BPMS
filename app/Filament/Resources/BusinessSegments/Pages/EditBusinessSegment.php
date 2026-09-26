<?php

namespace App\Filament\Resources\BusinessSegments\Pages;

use App\Filament\Resources\BusinessSegments\BusinessSegmentResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditBusinessSegment extends EditRecord
{
    protected static string $resource = BusinessSegmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
