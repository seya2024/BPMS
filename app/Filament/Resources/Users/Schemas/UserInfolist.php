<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('User Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('name')
                                    ->label('Full Name'),

                                TextEntry::make('email')
                                    ->label('Email Address'),

                                TextEntry::make('phone')
                                    ->label('Phone'),

                                TextEntry::make('job_title')
                                    ->label('Job Title'),

                                TextEntry::make('department')
                                    ->label('Department'),

                                TextEntry::make('created_at')
                                    ->label('Created At')
                                    ->dateTime(),
                            ]),
                    ]),

                Section::make('Group & Role Assignment')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('group.name')
                                    ->label('Primary Group')
                                    ->badge()
                                    ->color(fn ($state) => $state ? 'primary' : 'gray')
                                    ->default('No Group'),

                                TextEntry::make('groups.name')
                                    ->label('Additional Groups')
                                    ->badge()
                                    ->default('None'),
                            ]),
                    ]),

                Section::make('Organizational Assignment')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('primaryBranch.name')
                                    ->label('Primary Branch')
                                    ->badge()
                                    ->color('info')
                                    ->default('Unassigned'),

                                TextEntry::make('district.name')
                                    ->label('District')
                                    ->badge()
                                    ->color('warning')
                                    ->default('Unassigned'),
                            ]),
                    ]),

                Section::make('Account Status')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('status')
                                    ->label('Status')
                                    ->badge()
                                    ->color(fn ($state) => match ($state) {
                                        'active' => 'success',
                                        'inactive' => 'danger',
                                        'pending' => 'warning',
                                        'locked' => 'danger',
                                        default => 'gray',
                                    }),

                                IconEntry::make('is_active')
                                    ->label('Active')
                                    ->boolean(),

                                IconEntry::make('is_locked')
                                    ->label('Locked')
                                    ->boolean(),

                                IconEntry::make('mfa_enabled')
                                    ->label('MFA Enabled')
                                    ->boolean(),

                                IconEntry::make('must_change_password')
                                    ->label('Must Change Password')
                                    ->boolean(),

                                TextEntry::make('last_login_at')
                                    ->label('Last Login')
                                    ->dateTime()
                                    ->placeholder('Never'),
                            ]),
                    ]),

                Section::make('Permissions')
                    ->description('Permissions inherited from groups and roles')
                    ->schema([
                        TextEntry::make('permissions')
                            ->label('All Permissions')
                            ->badge()
                            ->formatStateUsing(function ($record) {
                                $directPermissions = $record->getDirectPermissions()->pluck('name');
                                $groupPermissions = $record->getGroupPermissions();
                                $allPermissions = $directPermissions->merge($groupPermissions)->unique();

                                if ($allPermissions->isEmpty()) {
                                    return 'No permissions';
                                }

                                return $allPermissions->join(', ');
                            })
                            ->columnSpanFull(),
                    ]),

                Section::make('Roles')
                    ->schema([
                        RepeatableEntry::make('roles')
                            ->label('')
                            ->schema([
                                TextEntry::make('name')
                                    ->label('Role')
                                    ->badge(),
                            ])
                            ->columns(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
