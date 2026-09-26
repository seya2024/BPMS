<?php

namespace App\Filament\Resources\BankingTypes\Pages;

use App\Filament\Resources\BankingTypes\BankingTypeResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditBankingType extends EditRecord
{
    protected static string $resource = BankingTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
