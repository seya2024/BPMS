<?php

namespace App\Filament\Resources\BankingTypes\Pages;

use App\Filament\Resources\BankingTypes\BankingTypeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBankingTypes extends ListRecords
{
    protected static string $resource = BankingTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
  
             CreateAction::make()->createAnother(true)->label('Add Baning Type')
                ->createAnother(true)
                ->icon('heroicon-o-plus')
                 ->outlined()
                 ->size('sm')
        ];
    }
}
