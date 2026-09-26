<?php

namespace App\Filament\Resources\BankingTypes\Pages;

use App\Filament\Resources\BankingTypes\BankingTypeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBankingType extends CreateRecord
{
    protected static string $resource = BankingTypeResource::class;
}
