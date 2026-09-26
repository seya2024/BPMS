<?php

namespace App\Filament\Resources\BankingTypes\Pages;

use App\Filament\Resources\BankingTypes\BankingTypeResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewBankingType extends ViewRecord
{
    protected static string $resource = BankingTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
