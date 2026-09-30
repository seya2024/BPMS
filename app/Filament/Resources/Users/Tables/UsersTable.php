<?php

namespace App\Filament\Resources\Users\Tables;

use App\Models\UserGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('group.name')
                    ->label('Primary Group')
                    ->badge()
                    ->color(fn ($state) => $state ? 'primary' : 'gray')
                    ->default('No Group')
                    ->sortable(),

                TextColumn::make('groups.name')
                    ->label('Additional Groups')
                    ->badge()
                    ->default('None')
                    ->toggleable(),

                TextColumn::make('primaryBranch.name')
                    ->label('Branch')
                    ->badge()
                    ->color('info')
                    ->default('Unassigned')
                    ->toggleable(),

                TextColumn::make('district.name')
                    ->label('District')
                    ->badge()
                    ->color('warning')
                    ->default('Unassigned')
                    ->toggleable(),

                TextColumn::make('department')
                    ->label('Department')
                    ->toggleable(),

                TextColumn::make('job_title')
                    ->label('Job Title')
                    ->toggleable(),

                TextColumn::make('permissions')
                    ->label('Permissions')
                    ->badge()
                    ->formatStateUsing(function ($record) {
                        $directPermissions = $record->getDirectPermissions()->pluck('name');
                        $groupPermissions = $record->getGroupPermissions();
                        $allPermissions = $directPermissions->merge($groupPermissions)->unique();

                        if ($allPermissions->isEmpty()) {
                            return 'No permissions';
                        }

                        return $allPermissions->take(3)->join(', ') . ($allPermissions->count() > 3 ? '...' : '');
                    })
                    ->toggleable(),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),

                IconColumn::make('is_locked')
                    ->label('Locked')
                    ->boolean()
                    ->sortable(),

                IconColumn::make('mfa_enabled')
                    ->label('MFA')
                    ->boolean()
                    ->toggleable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'active' => 'success',
                        'inactive' => 'danger',
                        'pending' => 'warning',
                        'locked' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('last_login_at')
                    ->label('Last Login')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('group_id')
                    ->label('Group')
                    ->relationship('group', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('branch_id')
                    ->label('Branch')
                    ->relationship('primaryBranch', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('district_id')
                    ->label('District')
                    ->relationship('district', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'pending' => 'Pending',
                        'locked' => 'Locked',
                    ]),

                TernaryFilter::make('is_active')
                    ->label('Active'),

                TernaryFilter::make('is_locked')
                    ->label('Locked'),

                TernaryFilter::make('mfa_enabled')
                    ->label('MFA Enabled'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    // Bulk Deactivate
                    \Filament\Actions\BulkAction::make('bulkDeactivate')
                        ->label('Deactivate')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(function ($records): void {
                            $records->each->update(['is_active' => false, 'status' => 'inactive']);
                            Notification::make()->success()->title($records->count() . ' users deactivated')->send();
                        }),

                    // Bulk Lock
                    \Filament\Actions\BulkAction::make('bulkLock')
                        ->label('Lock')
                        ->icon('heroicon-o-lock-closed')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(function ($records): void {
                            $records->each->update(['is_locked' => true, 'status' => 'locked', 'locked_at' => now()]);
                            Notification::make()->success()->title($records->count() . ' users locked')->send();
                        }),

                    // Bulk Unlock
                    \Filament\Actions\BulkAction::make('bulkUnlock')
                        ->label('Unlock')
                        ->icon('heroicon-o-lock-open')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function ($records): void {
                            $records->each->update(['is_locked' => false, 'status' => 'active', 'failed_login_attempts' => 0]);
                            Notification::make()->success()->title($records->count() . ' users unlocked')->send();
                        }),

                    // Bulk Reset Password
                    \Filament\Actions\BulkAction::make('bulkResetPassword')
                        ->label('Reset Password')
                        ->icon('heroicon-o-key')
                        ->color('warning')
                        ->form([
                            TextInput::make('new_password')
                                ->label('New Password')
                                ->password()
                                ->required()
                                ->confirmed()
                                ->minLength(8),
                            \Filament\Forms\Components\Toggle::make('force_change')
                                ->label('Force Password Change on Next Login')
                                ->default(false),
                        ])
                        ->action(function ($records, array $data): void {
                            $records->each->update([
                                'password' => bcrypt($data['new_password']),
                                'must_change_password' => $data['force_change'] ?? false,
                                'password_changed_at' => now(),
                            ]);
                            Notification::make()->success()->title($records->count() . ' passwords reset')->send();
                        }),

                    // Bulk Force Password Change
                    \Filament\Actions\BulkAction::make('bulkForcePasswordChange')
                        ->label('Force Password Change')
                        ->icon('heroicon-o-exclamation-triangle')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->action(function ($records): void {
                            $records->each->update(['must_change_password' => true]);
                            Notification::make()->success()->title($records->count() . ' users must change password')->send();
                        }),

                    // Bulk Assign Role
                    \Filament\Actions\BulkAction::make('bulkAssignRole')
                        ->label('Assign Role')
                        ->icon('heroicon-o-user-plus')
                        ->color('success')
                        ->form([
                            Select::make('role')
                                ->label('Role')
                                ->options(\Spatie\Permission\Models\Role::pluck('name', 'name'))
                                ->searchable()
                                ->required(),
                        ])
                        ->action(function ($records, array $data): void {
                            $records->each->assignRole($data['role']);
                            Notification::make()->success()->title($records->count() . ' users assigned role')->send();
                        }),

                    // Bulk Remove Role
                    \Filament\Actions\BulkAction::make('bulkRemoveRole')
                        ->label('Remove Role')
                        ->icon('heroicon-o-user-minus')
                        ->color('danger')
                        ->form([
                            Select::make('role')
                                ->label('Role')
                                ->options(\Spatie\Permission\Models\Role::pluck('name', 'name'))
                                ->searchable()
                                ->required(),
                        ])
                        ->action(function ($records, array $data): void {
                            $records->each->removeRole($data['role']);
                            Notification::make()->success()->title($records->count() . ' users removed role')->send();
                        }),

                    // Bulk Assign Branch
                    \Filament\Actions\BulkAction::make('bulkAssignBranch')
                        ->label('Assign Branch')
                        ->icon('heroicon-o-building-office')
                        ->color('info')
                        ->form([
                            Select::make('branch_id')
                                ->label('Branch')
                                ->options(\App\Models\Branch::pluck('name', 'id'))
                                ->searchable()
                                ->required(),
                            \Filament\Forms\Components\Toggle::make('is_primary')
                                ->label('Set as Primary Branch')
                                ->default(false),
                        ])
                        ->action(function ($records, array $data): void {
                            foreach ($records as $record) {
                                $record->branches()->syncWithoutDetaching([
                                    $data['branch_id'] => ['is_primary' => $data['is_primary'] ?? false]
                                ]);
                                if ($data['is_primary'] ?? false) {
                                    $record->update(['branch_id' => $data['branch_id']]);
                                }
                            }
                            Notification::make()->success()->title($records->count() . ' users assigned branch')->send();
                        }),

                    // Bulk Assign District
                    \Filament\Actions\BulkAction::make('bulkAssignDistrict')
                        ->label('Assign District')
                        ->icon('heroicon-o-map')
                        ->color('warning')
                        ->form([
                            Select::make('district_id')
                                ->label('District')
                                ->options(\App\Models\District::pluck('name', 'id'))
                                ->searchable()
                                ->required(),
                        ])
                        ->action(function ($records, array $data): void {
                            $records->each->update(['district_id' => $data['district_id']]);
                            Notification::make()->success()->title($records->count() . ' users assigned district')->send();
                        }),

                    // Bulk Add to Group
                    \Filament\Actions\BulkAction::make('bulkAddToGroup')
                        ->label('Add to Group')
                        ->icon('heroicon-o-user-group')
                        ->color('success')
                        ->form([
                            Select::make('group_id')
                                ->label('Select Group')
                                ->options(UserGroup::active()->pluck('name', 'id'))
                                ->searchable()
                                ->required(),
                        ])
                        ->action(function ($records, array $data): void {
                            foreach ($records as $record) {
                                $record->groups()->syncWithoutDetaching([$data['group_id']]);
                            }
                            Notification::make()->success()->title($records->count() . ' users added to group')->send();
                        }),

                    // Bulk Remove from Group
                    \Filament\Actions\BulkAction::make('bulkRemoveFromGroup')
                        ->label('Remove from Group')
                        ->icon('heroicon-o-user-minus')
                        ->color('danger')
                        ->form([
                            Select::make('group_id')
                                ->label('Select Group')
                                ->options(UserGroup::active()->pluck('name', 'id'))
                                ->searchable()
                                ->required(),
                        ])
                        ->action(function ($records, array $data): void {
                            foreach ($records as $record) {
                                $record->groups()->detach($data['group_id']);
                            }
                            Notification::make()->success()->title($records->count() . ' users removed from group')->send();
                        }),

                    // Bulk Revoke Sessions
                    \Filament\Actions\BulkAction::make('bulkRevokeSessions')
                        ->label('Revoke Sessions')
                        ->icon('heroicon-o-arrow-right-on-rectangle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(function ($records): void {
                            foreach ($records as $record) {
                                $record->tokens()->delete();
                            }
                            Notification::make()->success()->title($records->count() . ' sessions revoked')->send();
                        }),

                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
