<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TargetApprovalNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @param  string  $planType  'Annual', 'Deposit', or 'Account'
     * @param  int  $planId
     * @param  string  $action  'submitted', 'approved', or 'rejected'
     * @param  string  $userName
     */
    public function __construct(
        public string $planType,
        public int $planId,
        public string $action,
        public string $userName,
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
        $subject = match ($this->action) {
            'submitted' => "{$this->planType} Target Submitted for Approval",
            'approved' => "{$this->planType} Target Approved",
            'rejected' => "{$this->planType} Target Rejected",
            default => "{$this->planType} Target Update",
        };

        $greeting = match ($this->action) {
            'submitted' => 'A new target has been submitted for your review.',
            'approved' => 'Your target has been approved!',
            'rejected' => 'Your target has been rejected.',
            default => 'There is an update on your target.',
        };

        return (new MailMessage)
            ->subject($subject)
            ->greeting("Hello {$notifiable->name}!")
            ->line($greeting)
            ->line("**Plan Type:** {$this->planType}")
            ->line("**Plan ID:** {$this->planId}")
            ->line("**Action:** " . ucfirst($this->action))
            ->line("**By:** {$this->userName}")
            ->action('View Details', url('/'))
            ->line('Thank you for using the BPMS system.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'plan_type' => $this->planType,
            'plan_id' => $this->planId,
            'action' => $this->action,
            'user_name' => $this->userName,
            'message' => "{$this->planType} target #{$this->planId} has been {$this->action} by {$this->userName}.",
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
