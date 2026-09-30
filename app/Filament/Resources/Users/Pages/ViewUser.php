<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Models\UserGroup;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
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
                    Notification::make()->success()->title('User Activated')->send();
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
                    Notification::make()->success()->title('User Deactivated')->send();
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
                    Notification::make()->success()->title('User Locked')->send();
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
                    Notification::make()->success()->title('User Unlocked')->send();
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
                    Notification::make()->success()->title('Password Reset')->send();
                }),

            // Force Password Change
            Action::make('forcePasswordChange')
                ->label('Force Password Change')
                ->icon('heroicon-o-exclamation-triangle')
                ->color('warning')
                ->requiresConfirmation()
                ->action(function (): void {
                    $this->record->update(['must_change_password' => true]);
                    Notification::make()->success()->title('Password Change Forced')->send();
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
                    Notification::make()->success()->title('Role Assigned')->send();
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
                    Notification::make()->success()->title('Role Removed')->send();
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
                    Notification::make()->success()->title('Branch Assigned')->send();
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
                    Notification::make()->success()->title('District Assigned')->send();
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
                    Notification::make()->success()->title('Added to Group')->send();
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
                    Notification::make()->success()->title('Removed from Group')->send();
                })
                ->visible(fn () => $this->record->groups->isNotEmpty()),

            // Revoke Sessions
            Action::make('revokeSessions')
                ->label('Revoke Sessions')
                ->icon('heroicon-o-arrow-right-on-rectangle')
                ->color('danger')
                ->requiresConfirmation()
                ->action(function (): void {
                    $this->record->tokens()->delete();
                    Notification::make()->success()->title('Sessions Revoked')->send();
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
                    Notification::make()->success()->title('User Approved')->send();
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
                    Notification::make()->danger()->title('User Rejected')->send();
                })
                ->visible(fn () => $this->record->isPending()),
        ];
    }
}
