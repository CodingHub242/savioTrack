<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Channels\Channel;
use App\Models\Goal;

class ArkeselDepositReminder extends Notification
{
    public function __construct(public Goal $goal, public ?string $channelName = null)
    {
    }

    public function via($notifiable): array
    {
        return ['arkesel'];
    }

    public function toArkesel($notifiable)
    {
        $schedule = match ($this->goal->deposit_frequency) {
            'daily' => 'daily',
            'weekly' => 'weekly',
            'monthly' => 'monthly',
            default => 'one-time',
        };

        return (object) [
            'to' => $notifiable->phone_number ?? $this->goal->phone_number,
            'message' => "SavioTrack: Reminder to make your {$schedule} deposit of \${$this->goal->target_amount} for goal '{$this->goal->name}'. Reply STOP to opt out.",
            'from' => 'SavioTrack',
        ];
    }
}

class ArkeselChannel implements Channel
{
    protected string $apiKey;
    protected string $apiUrl;
    protected string $senderId;

    public function __construct()
    {
        $this->apiKey = config('services.arkesel.api_key', env('ARKESAL_API_KEY', ''));
        $this->apiUrl = config('services.arkesel.api_url', env('ARKESAL_API_URL', 'https://sms.arkesel.com/api/v3/send/'));
        $this->senderId = config('services.arkesel.sender_id', env('ARKESAL_SENDER_ID', 'SavioTrack'));
    }

    public function send($notifiable, $notification): void
    {
        if (! $this->apiKey) {
            return;
        }

        $message = $notification->toArkesel($notifiable);

        $payload = [
            'apikey' => $this->apiKey,
            'sender' => $this->senderId,
            'to' => $message->to,
            'message' => $message->message,
        ];

        try {
            \Illuminate\Support\Facades\Http::post($this->apiUrl, $payload);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Arkesel SMS notification failed: ' . $e->getMessage());
        }
    }
}
