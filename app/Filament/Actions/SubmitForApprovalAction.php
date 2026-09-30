<?php

namespace App\Filament\Actions;

use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;

class SubmitForApprovalAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'submit_for_approval';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Submit for Approval')
            ->icon('heroicon-o-paper-airplane')
            ->color('warning')
            ->requiresConfirmation()
            ->modalHeading('Submit for Approval')
            ->modalDescription('Are you sure you want to submit this plan for approval?')
            ->form([
                Textarea::make('remarks')
                    ->label('Remarks')
                    ->nullable()
                    ->maxLength(500),
            ])
            ->action(function (Model $record, array $data): void {
                $record->update([
                    'approval_status' => 'pending',
                    'approval_remarks' => $data['remarks'] ?? null,
                ]);

                // Notify admins
                $admins = \App\Models\User::role('admin')->get();
                foreach ($admins as $admin) {
                    $admin->notify(new \App\Notifications\TargetApprovalNotification(
                        class_basename($record),
                        $record->id,
                        'submitted',
                        auth()->user()->name
                    ));
                }

                Notification::make()
                    ->success()
                    ->title('Submitted for Approval')
                    ->body('The plan has been submitted for approval.')
                    ->send();
            })
            ->visible(fn (Model $record) => $record->canBeSubmitted());
    }
}
