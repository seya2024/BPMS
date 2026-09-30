<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MilestoneAchievedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @param  string  $branchName
     * @param  string  $milestoneType  'deposit' or 'account'
     * @param  float  $achievementPercentage
     * @param  float  $targetAmount
     * @param  float  $actualAmount
     */
    public function __construct(
        public string $branchName,
        public string $milestoneType,
        public float $achievementPercentage,
        public float $targetAmount,
        public float $actualAmount,
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $typeLabel = $this->milestoneType === 'deposit' ? 'Deposit' : 'Account';

        return (new MailMessage)
            ->subject("Milestone Achieved: {$this->branchName}")
            ->greeting("Hello {$notifiable->name}!")
            ->line("Great news! Branch **{$this->branchName}** has achieved a {$this->achievementPercentage}% milestone for {$typeLabel} targets.")
            ->line("**Target Amount:** " . number_format($this->targetAmount, 2))
            ->line("**Actual Amount:** " . number_format($this->actualAmount, 2))
            ->line("**Achievement:** {$this->achievementPercentage}%")
            ->action('View Details', url('/'))
            ->line('Keep up the excellent work!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'branch_name' => $this->branchName,
            'milestone_type' => $this->milestoneType,
            'achievement_percentage' => $this->achievementPercentage,
            'target_amount' => $this->targetAmount,
            'actual_amount' => $this->actualAmount,
            'message' => "Branch {$this->branchName} achieved {$this->achievementPercentage}% of {$this->milestoneType} target.",
        ];
    }

    /**
     * Get the database representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return $this->toArray($notifiable);
    }
}
