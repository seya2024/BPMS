<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section as SchemaSection;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                SchemaSection::make('User Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpan(1),

                                TextInput::make('email')
                                    ->email()
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true)
                                    ->columnSpan(1),

                                TextInput::make('password')
                                    ->password()
                                    ->required(fn (string $context): bool => $context === 'create')
                                    ->confirmed()
                                    ->minLength(8)
                                    ->columnSpan(1),

                                TextInput::make('phone')
                                    ->tel()
                                    ->maxLength(20)
                                    ->columnSpan(1),

                                TextInput::make('job_title')
                                    ->label('Job Title')
                                    ->maxLength(255)
                                    ->columnSpan(1),

                                TextInput::make('department')
                                    ->maxLength(255)
                                    ->columnSpan(1),
                            ]),
                    ]),

                SchemaSection::make('Group & Role Assignment')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('group_id')
                                    ->label('Primary Group')
                                    ->relationship('group', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->columnSpan(1),

                                Select::make('groups')
                                    ->label('Additional Groups')
                                    ->relationship('groups', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->multiple()
                                    ->columnSpan(1),
                            ]),
                    ]),

                SchemaSection::make('Organizational Assignment')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('branch_id')
                                    ->label('Primary Branch')
                                    ->relationship('primaryBranch', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->columnSpan(1),

                                Select::make('district_id')
                                    ->label('District')
                                    ->relationship('district', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->columnSpan(1),
                            ]),
                    ]),

                SchemaSection::make('Account Settings')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Select::make('status')
                                    ->label('Status')
                                    ->options([
                                        'active' => 'Active',
                                        'inactive' => 'Inactive',
                                        'pending' => 'Pending',
                                        'locked' => 'Locked',
                                    ])
                                    ->default('active')
                                    ->columnSpan(1),

                                Toggle::make('is_active')
                                    ->label('Active')
                                    ->default(true)
                                    ->columnSpan(1),

                                Toggle::make('must_change_password')
                                    ->label('Force Password Change')
                                    ->default(false)
                                    ->columnSpan(1),
                            ]),
                    ]),
            ]);
    }
}
