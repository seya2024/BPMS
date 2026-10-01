<?php

namespace App\Filament\Resources\DailyForeignCurrencyGenerations\Schemas;

use App\Filament\Support\BusinessDay;
use App\Models\Branch;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class DailyForeignCurrencyGenerationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                BusinessDay::picker(),

                Repeater::make('branches')
                    ->label('Daily Foreign Currency Generation')
                    ->collapsible()
                    ->collapsed()
                    ->itemLabel(fn (array $state): ?string => $state['branch_name'] ?? 'Foreign Currency Generation')
                    ->default(
                        Branch::orderBy('name')
                            ->get()
                            ->map(fn ($branch) => [
                                'branch_id' => $branch->id,
                                'branch_name' => $branch->name,
                                'amount' => 0,
                                'target_amount' => 0,
                                'currency_code' => 'ETB',
                                'remarks' => null,
                            ])
                            ->toArray()
                    )
                    ->schema([

                        Hidden::make('branch_id'),

                        TextInput::make('branch_name')
                            ->label('Branch')
                            ->readOnly()
                            ->dehydrated(false),

                        TextInput::make('amount')
                            ->label('Amount')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->required(),

                        TextInput::make('target_amount')
                            ->label('Target')
                            ->numeric()
                            ->default(0)
                            ->minValue(0),

                        Select::make('currency_code')
                            ->label('Currency')
                            ->options([
                                'ETB' => 'ETB (Ethiopian Birr)',
                                'USD' => 'USD (US Dollar)',
                                'EUR' => 'EUR (Euro)',
                                'GBP' => 'GBP (British Pound)',
                            ])
                            ->default('ETB')
                            ->required(),

                        Textarea::make('remarks')
                            ->rows(2)
                            ->columnSpanFull(),

                    ])
                    ->columns(2)
                    ->addable(false)
                    ->deletable(false)
                    ->reorderable(false)
                    ->columnSpanFull(),

            ])
            ->columns(1);
    }
}