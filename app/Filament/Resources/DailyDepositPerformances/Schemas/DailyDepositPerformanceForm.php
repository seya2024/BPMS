<?php

namespace App\Filament\Resources\DailyDepositPerformances\Schemas;

use App\Filament\Support\BusinessDay;
use App\Models\Branch;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class DailyDepositPerformanceForm
{
    public static function configure(Schema $schema): Schema {
    

        return $schema
            ->components([

                BusinessDay::picker(),

                Repeater::make('branches')
                    ->label('Daily Deposit Performance')
                    ->collapsible()
                    ->collapsed()
                    ->itemLabel(fn (array $state): ?string => $state['branch_name'] ?? 'Deposit Performance')
                    ->default(
                        Branch::orderBy('name')
                            ->get()
                            ->map(fn ($branch) => [
                                'branch_id' => $branch->id,
                                'branch_name' => $branch->name,
                                'total_deposit_amount' => 0,
                                'new_deposit_amount' => 0,
                                'deposit_inflow_amount' => 0,
                                'deposit_outflow_amount' => 0,
                                'net_deposit_change' => 0,
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

                        TextInput::make('total_deposit_amount')
                            ->label('Total Deposit')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->required(),

                        TextInput::make('new_deposit_amount')
                            ->label('New Deposit')
                            ->numeric()
                            ->default(0)
                            ->minValue(0),


                    TextInput::make('deposit_inflow_amount')
                        ->label('Inflow')
                        ->numeric()
                        ->default(0)
                        ->minValue(0)
                        ->live()
                        ->afterStateUpdated(function (Get $get, Set $set) {

                            $inflow = (float) ($get('deposit_inflow_amount') ?? 0);
                            $outflow = (float) ($get('deposit_outflow_amount') ?? 0);

                            $set('net_deposit_change', $inflow - $outflow);
                        }),

                    TextInput::make('deposit_outflow_amount')
                        ->label('Outflow')
                        ->numeric()
                        ->default(0)
                        ->minValue(0)
                        ->live()
                        ->afterStateUpdated(function (Get $get, Set $set) {

                            $inflow = (float) ($get('deposit_inflow_amount') ?? 0);
                            $outflow = (float) ($get('deposit_outflow_amount') ?? 0);

                            $set('net_deposit_change', $inflow - $outflow);
                        }),

                    TextInput::make('net_deposit_change')
                        ->label('Net Change')
                        ->numeric()
                        ->readOnly()
                        ->live()
                        ->dehydrated(),
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