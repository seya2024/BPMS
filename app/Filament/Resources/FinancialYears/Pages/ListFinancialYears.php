<?php

namespace App\Filament\Resources\FinancialYears\Pages;

use App\Filament\Resources\FinancialYears\FinancialYearResource;
use App\Models\FinancialYear;
use App\Services\FinancialPeriodService;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListFinancialYears extends ListRecords
{
    protected static string $resource = FinancialYearResource::class;

    protected function getHeaderActions(): array
    {
        return [
        
    CreateAction::make()
    ->label('Add New FY')
    ->icon('heroicon-o-plus')
    ->outlined()
    ->size('sm')
    ->createAnother(true)

    ->visible(fn () =>
        ! FinancialYear::where('status', 'OPEN')->exists()
    ),

        Action::make('generate_periods')
            ->label('Generate Quarters')
            ->requiresConfirmation()
              // ✅ show only if OPEN FY exists
                ->visible(fn () =>
                    FinancialYear::where('status', 'OPEN')->exists()
                )
  
    ->icon('heroicon-o-plus')
    ->outlined()
    ->size('sm')

            ->form([
                \Filament\Forms\Components\Select::make('financial_year_id')
                    ->label('Financial Year')
                    ->options(
                        \App\Models\FinancialYear::query()
                            ->where('status', 'OPEN')
                            ->pluck('name', 'id')
                    )
                    ->searchable()
                    ->required(),
            ])
            ->action(function (array $data) {

                $year = \App\Models\FinancialYear::findOrFail($data['financial_year_id']);

                // ❗ Prevent duplicate generation
                if ($year->periods()->exists()) {
                    Notification::make()
                        ->title('Already Generated')
                        ->body('Financial periods already exist for this year.')
                        ->warning()
                        ->send();

                    return;
                }

                app(FinancialPeriodService::class)
                    ->generate($year);

                Notification::make()
                    ->title('Success')
                    ->body('Financial quarters generated successfully.')
                    ->success()
                    ->send();
            }),

     ];
    }
}
