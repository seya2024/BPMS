<?php

namespace App\Filament\Resources\DailySuperAppSubscriptions\Schemas;

use App\Filament\Support\BusinessDay;
use App\Models\Branch;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class DailySuperAppSubscriptionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                BusinessDay::picker(),

                Repeater::make('branches')
                    ->label('Daily Super App Subscriptions')
                    ->collapsible()
                    ->collapsed()
                    ->itemLabel(fn (array $state): ?string => $state['branch_name'] ?? 'Super App Subscription')
                    ->default(
                        Branch::orderBy('name')
                            ->get()
                            ->map(fn ($branch) => [
                                'branch_id' => $branch->id,
                                'branch_name' => $branch->name,
                                'subscriptions' => 0,
                                'target_subscriptions' => 0,
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

                        TextInput::make('subscriptions')
                            ->label('Subscriptions')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->required(),

                        TextInput::make('target_subscriptions')
                            ->label('Target')
                            ->numeric()
                            ->default(0)
                            ->minValue(0),

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