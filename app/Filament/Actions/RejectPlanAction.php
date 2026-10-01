<?php

namespace App\Filament\Actions;

use App\Filament\Support\Notify;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Illuminate\Database\Eloquent\Model;

class RejectPlanAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'reject';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Reject')
            ->icon('heroicon-o-x-circle')
            ->color('danger')
            ->requiresConfirmation()
            ->modalHeading('Reject Plan')
            ->modalDescription('Are you sure you want to reject this plan?')
            ->form([
                Textarea::make('approval_remarks')
                    ->label('Rejection Reason')
                    ->required()
                    ->maxLength(500),
            ])
            ->action(function (Model $record, array $data): void {
                $record->update([
                    'approval_status' => 'rejected',
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
                            'rejected',
                            auth()->user()->name
                        ));
                    }
                }

                // warning(), not danger(): rejecting a plan is a normal review
                // outcome. Red here would read as a system failure.
                Notify::declined(
                    'Plan rejected',
                    'The plan was sent back for revision and does not count towards attainment until it is resubmitted.'
                );
            })
            ->visible(fn (Model $record) => $record->canBeApproved());
    }
}

