<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class PerformanceAlertNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @param  string  $branchName
     * @param  string  $alertType  'below_target' or 'above_target'
     * @param  string  $metricName
     * @param  float  $currentValue
     * @param  float  $targetValue
     * @param  float  $percentage
     */
    public function __construct(
        public string $branchName,
        public string $alertType,
        public string $metricName,
        public float $currentValue,
        public float $targetValue,
        public float $percentage,
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $isBelowTarget = $this->alertType === 'below_target';

        return [
            'branch_name' => $this->branchName,
            'alert_type' => $this->alertType,
            'metric_name' => $this->metricName,
            'current_value' => $this->currentValue,
            'target_value' => $this->targetValue,
            'percentage' => $this->percentage,
            'message' => $isBelowTarget
                ? "ALERT: Branch {$this->branchName} is below target for {$this->metricName}. Current: " . number_format($this->currentValue, 2) . ", Target: " . number_format($this->targetValue, 2) . " ({$this->percentage}%)"
                : "CONGRATULATIONS: Branch {$this->branchName} has exceeded target for {$this->metricName}. Current: " . number_format($this->currentValue, 2) . ", Target: " . number_format($this->targetValue, 2) . " ({$this->percentage}%)",
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
