<?php

namespace App\Filament\Resources\Users\Tables;

use App\Filament\Support\Notify;
use App\Models\UserGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
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
                            Notify::done(
            'Users deactivated',
            $records->count() . ' ' . Notify::plural($records->count(), 'user', 'users')
                . ' deactivated. Their data is kept and the accounts can be reactivated.'
        );
                        }),

                    // Bulk Lock
                    \Filament\Actions\BulkAction::make('bulkLock')
                        ->label('Lock')
                        ->icon('heroicon-o-lock-closed')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(function ($records): void {
                            $records->each->update(['is_locked' => true, 'status' => 'locked', 'locked_at' => now()]);
                            Notify::done(
            'Users locked',
            $records->count() . ' ' . Notify::plural($records->count(), 'user is', 'users are')
                . ' now locked out until an administrator unlocks them.'
        );
                        }),

                    // Bulk Unlock
                    \Filament\Actions\BulkAction::make('bulkUnlock')
                        ->label('Unlock')
                        ->icon('heroicon-o-lock-open')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function ($records): void {
                            $records->each->update(['is_locked' => false, 'status' => 'active', 'failed_login_attempts' => 0]);
                            Notify::done(
            'Users unlocked',
            $records->count() . ' ' . Notify::plural($records->count(), 'user', 'users')
                . ' can sign in again. Failed sign-in attempts were reset to zero.'
        );
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
                            Notify::done(
            'Passwords reset',
            'A new password was set for ' . $records->count() . ' '
                . Notify::plural($records->count(), 'user', 'users') . '. Share them securely.'
        );
                        }),

                    // Bulk Force Password Change
                    \Filament\Actions\BulkAction::make('bulkForcePasswordChange')
                        ->label('Force Password Change')
                        ->icon('heroicon-o-exclamation-triangle')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->action(function ($records): void {
                            $records->each->update(['must_change_password' => true]);
                            Notify::done(
            'Password change required',
            $records->count() . ' ' . Notify::plural($records->count(), 'user', 'users')
                . ' will be asked to set a new password at their next sign-in.'
        );
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
                            Notify::done(
            'Role assigned',
            'The ' . $data['role'] . ' role was assigned to ' . $records->count() . ' '
                . Notify::plural($records->count(), 'user', 'users') . '. Permissions apply immediately.'
        );
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
                            Notify::declined(
            'Role removed',
            'The ' . $data['role'] . ' role was removed from ' . $records->count() . ' '
                . Notify::plural($records->count(), 'user', 'users') . '.'
        );
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
                            Notify::done(
            'Branch assigned',
            $records->count() . ' ' . Notify::plural($records->count(), 'user', 'users')
                . ' assigned to the selected branch.'
        );
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
                            Notify::done(
            'District assigned',
            $records->count() . ' ' . Notify::plural($records->count(), 'user', 'users')
                . ' assigned to the selected district.'
        );
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
                            Notify::done(
            'Added to group',
            $records->count() . ' ' . Notify::plural($records->count(), 'user', 'users')
                . ' added to the selected group.'
        );
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
                            Notify::declined(
            'Removed from group',
            $records->count() . ' ' . Notify::plural($records->count(), 'user', 'users')
                . ' removed from the selected group.'
        );
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
                            Notify::done(
            'Sessions revoked',
            'Active sessions were ended for ' . $records->count() . ' '
                . Notify::plural($records->count(), 'user', 'users') . '. They will need to sign in again.'
        );
                        }),

                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

