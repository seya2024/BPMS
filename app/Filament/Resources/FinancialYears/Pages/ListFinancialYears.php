<?php

namespace App\Filament\Resources\FinancialYears\Pages;

use App\Filament\Resources\FinancialYears\FinancialYearResource;
use App\Filament\Support\Notify;
use App\Models\FinancialYear;
use App\Services\FinancialPeriodService;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Throwable;

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

                // Prevent duplicate generation: a year that already has periods
                // is left untouched rather than having its quarters replaced.
                if ($year->periods()->exists()) {
                    Notify::declined(
                        'Quarters already exist',
                        $year->name . ' already has financial periods, so nothing was changed. Delete the existing periods first if you need to regenerate them.'
                    );

                    return;
                }

                try {
                    $created = app(FinancialPeriodService::class)
                        ->generate($year);
                } catch (Throwable $e) {
                    Notify::saveFailed('financial periods', $e);

                    return;
                }

                Notify::done(
                    'Quarters generated',
                    $created . ' ' . Notify::plural($created, 'quarter was', 'quarters were')
                        . ' created for ' . $year->name . '.'
                );
            }),

     ];
    }
}

