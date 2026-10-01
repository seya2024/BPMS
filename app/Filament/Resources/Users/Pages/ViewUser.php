<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Filament\Support\Notify;
use App\Models\UserGroup;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Pages\ViewRecord;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),

            // Activate
            Action::make('activate')
                ->label('Activate')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->action(function (): void {
                    $this->record->update(['is_active' => true, 'status' => 'active']);
                    Notify::done(
                        'User activated',
                        $this->record->name . ' can now sign in and use the system.'
                    );
                })
                ->visible(fn () => !$this->record->is_active || $this->record->status !== 'active'),

            // Deactivate
            Action::make('deactivate')
                ->label('Deactivate')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->action(function (): void {
                    $this->record->update(['is_active' => false, 'status' => 'inactive']);
                    Notify::done(
                        'User deactivated',
                        $this->record->name . ' can no longer sign in. Their data is kept and the account can be reactivated.'
                    );
                })
                ->visible(fn () => $this->record->is_active && $this->record->status === 'active'),

            // Lock
            Action::make('lock')
                ->label('Lock')
                ->icon('heroicon-o-lock-closed')
                ->color('danger')
                ->requiresConfirmation()
                ->action(function (): void {
                    $this->record->update(['is_locked' => true, 'status' => 'locked', 'locked_at' => now()]);
                    Notify::done(
                        'User locked',
                        $this->record->name . ' is locked out until an administrator unlocks the account.'
                    );
                })
                ->visible(fn () => !$this->record->is_locked),

            // Unlock
            Action::make('unlock')
                ->label('Unlock')
                ->icon('heroicon-o-lock-open')
                ->color('success')
                ->requiresConfirmation()
                ->action(function (): void {
                    $this->record->update(['is_locked' => false, 'status' => 'active', 'failed_login_attempts' => 0]);
                    Notify::done(
                        'User unlocked',
                        $this->record->name . ' can sign in again. Failed sign-in attempts have been reset to zero.'
                    );
                })
                ->visible(fn () => $this->record->is_locked),

            // Reset Password
            Action::make('resetPassword')
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
                    Toggle::make('force_change')
                        ->label('Force Password Change on Next Login')
                        ->default(false),
                ])
                ->action(function (array $data): void {
                    $this->record->update([
                        'password' => bcrypt($data['new_password']),
                        'must_change_password' => $data['force_change'] ?? false,
                        'password_changed_at' => now(),
                    ]);
                    Notify::done(
                        'Password reset',
                        ($data['force_change'] ?? false)
                            ? 'A new password was set for ' . $this->record->name . '. They must choose a new one at next sign-in.'
                            : 'A new password was set for ' . $this->record->name . '. Share it with them securely.'
                    );
                }),

            // Force Password Change
            Action::make('forcePasswordChange')
                ->label('Force Password Change')
                ->icon('heroicon-o-exclamation-triangle')
                ->color('warning')
                ->requiresConfirmation()
                ->action(function (): void {
                    $this->record->update(['must_change_password' => true]);
                    Notify::done(
                        'Password change required',
                        $this->record->name . ' will be asked to set a new password at their next sign-in.'
                    );
                })
                ->visible(fn () => !$this->record->must_change_password),

            // Assign Role
            Action::make('assignRole')
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
                ->action(function (array $data): void {
                    $this->record->assignRole($data['role']);
                    Notify::done(
                        'Role assigned',
                        $this->record->name . ' now holds the ' . $data['role'] . ' role. Their permissions apply immediately.'
                    );
                }),

            // Remove Role
            Action::make('removeRole')
                ->label('Remove Role')
                ->icon('heroicon-o-user-minus')
                ->color('danger')
                ->form([
                    Select::make('role')
                        ->label('Role')
                        ->options($this->record->roles->pluck('name', 'name'))
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $this->record->removeRole($data['role']);
                    Notify::declined(
                        'Role removed',
                        'The ' . $data['role'] . ' role was taken from ' . $this->record->name . '.'
                            . ($this->record->roles->isEmpty() ? ' They now have no roles and cannot access the system.' : '')
                    );
                })
                ->visible(fn () => $this->record->roles->isNotEmpty()),

            // Assign Branch
            Action::make('assignBranch')
                ->label('Assign Branch')
                ->icon('heroicon-o-building-office')
                ->color('info')
                ->form([
                    Select::make('branch_id')
                        ->label('Branch')
                        ->options(\App\Models\Branch::pluck('name', 'id'))
                        ->searchable()
                        ->required(),
                    Toggle::make('is_primary')
                        ->label('Set as Primary Branch')
                        ->default(false),
                ])
                ->action(function (array $data): void {
                    $this->record->branches()->syncWithoutDetaching([
                        $data['branch_id'] => ['is_primary' => $data['is_primary'] ?? false]
                    ]);
                    if ($data['is_primary'] ?? false) {
                        $this->record->update(['branch_id' => $data['branch_id']]);
                    }

                    $branchName = \App\Models\Branch::whereKey($data['branch_id'])->value('name') ?? 'the selected branch';

                    Notify::done(
                        'Branch assigned',
                        ($data['is_primary'] ?? false)
                            ? $branchName . ' is now ' . $this->record->name . "'s primary branch."
                            : $branchName . ' was added to ' . $this->record->name . "'s branch assignments."
                    );
                }),

            // Assign District
            Action::make('assignDistrict')
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
                ->action(function (array $data): void {
                    $this->record->update(['district_id' => $data['district_id']]);
                    $districtName = \App\Models\District::whereKey($data['district_id'])->value('name') ?? 'the selected district';
                    Notify::done(
                        'District assigned',
                        $this->record->name . ' is now assigned to ' . $districtName . '.'
                    );
                }),

            // Add to Group
            Action::make('addToGroup')
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
                ->action(function (array $data): void {
                    $this->record->groups()->syncWithoutDetaching([$data['group_id']]);
                    $groupName = UserGroup::whereKey($data['group_id'])->value('name') ?? 'the selected group';
                    Notify::done(
                        'Added to group',
                        $this->record->name . ' now belongs to ' . $groupName . '.'
                    );
                }),

            // Remove from Group
            Action::make('removeFromGroup')
                ->label('Remove from Group')
                ->icon('heroicon-o-user-minus')
                ->color('danger')
                ->form([
                    Select::make('group_id')
                        ->label('Select Group')
                        ->options($this->record->groups->pluck('name', 'id'))
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $this->record->groups()->detach($data['group_id']);
                    $groupName = UserGroup::whereKey($data['group_id'])->value('name') ?? 'the group';
                    Notify::declined(
                        'Removed from group',
                        $this->record->name . ' no longer belongs to ' . $groupName . '.'
                    );
                })
                ->visible(fn () => $this->record->groups->isNotEmpty()),

            // Revoke Sessions
            Action::make('revokeSessions')
                ->label('Revoke Sessions')
                ->icon('heroicon-o-arrow-right-on-rectangle')
                ->color('danger')
                ->requiresConfirmation()
                ->action(function (): void {
                    $count = $this->record->tokens()->delete();
                    Notify::done(
                        'Sessions revoked',
                        $count > 0
                            ? $count . ' active ' . Notify::plural($count, 'session', 'sessions') . ' ended. ' . $this->record->name . ' will need to sign in again.'
                            : 'There were no active sessions to end for ' . $this->record->name . '.'
                    );
                }),

            // Approve
            Action::make('approve')
                ->label('Approve')
                ->icon('heroicon-o-check-badge')
                ->color('success')
                ->requiresConfirmation()
                ->action(function (): void {
                    $this->record->update([
                        'status' => 'active',
                        'is_active' => true,
                        'approved_by' => auth()->id(),
                        'approved_at' => now(),
                    ]);
                    Notify::done(
                        'User approved',
                        $this->record->name . ' is now active and can sign in.'
                    );
                })
                ->visible(fn () => $this->record->isPending()),

            // Reject
            Action::make('reject')
                ->label('Reject')
                ->icon('heroicon-o-x-mark')
                ->color('danger')
                ->requiresConfirmation()
                ->action(function (): void {
                    $this->record->update(['status' => 'inactive', 'is_active' => false]);
                    // warning(), not danger(): the rejection was requested and
                    // carried out correctly, so this is an expected outcome rather
                    // than a system failure.
                    Notify::declined(
                        'User rejected',
                        'The request from ' . $this->record->name . ' was declined. The account stays inactive until approved.'
                    );
                })
                ->visible(fn () => $this->record->isPending()),
        ];
    }
}

