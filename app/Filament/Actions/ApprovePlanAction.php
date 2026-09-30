<?php

namespace App\Filament\Actions;

use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;

class ApprovePlanAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'approve';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Approve')
            ->icon('heroicon-o-check-circle')
            ->color('success')
            ->requiresConfirmation()
            ->modalHeading('Approve Plan')
            ->modalDescription('Are you sure you want to approve this plan?')
            ->form([
                Textarea::make('approval_remarks')
                    ->label('Approval Remarks')
                    ->nullable()
                    ->maxLength(500),
            ])
            ->action(function (Model $record, array $data): void {
                $record->update([
                    'approval_status' => 'approved',
                    'approved_by' => auth()->id(),
                    'approved_at' => now(),
                    'approval_remarks' => $data['approval_remarks'] ?? null,
                ]);

                // Notify the creator
                if ($record->created_by) {
                    $creator = \App\Models\User::find($record->created_by);
                    if ($creator) {
                        $creator->notify(new \App\Notifications\TargetApprovalNotification(
                            class_basename($record),
                            $record->id,
                            'approved',
                            auth()->user()->name
                        ));
                    }
                }

                Notification::make()
                    ->success()
                    ->title('Plan Approved')
                    ->body('The plan has been approved successfully.')
                    ->send();
            })
            ->visible(fn (Model $record) => $record->canBeApproved());
    }
}
