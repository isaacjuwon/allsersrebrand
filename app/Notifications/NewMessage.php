<?php

namespace App\Notifications;

use App\Models\Message;
use App\Services\OneSignalService;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewMessage extends Notification
{
    use Queueable;

    protected $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    public function via(object $notifiable): array
    {
        // Trigger OneSignal push if user has a player ID
        if ($notifiable->onesignal_player_id) {
            $this->sendPushNotification($notifiable);
        }

        return ['database'];
    }

    protected function sendPushNotification($notifiable)
    {
        $oneSignal = app(OneSignalService::class);
        $senderName = $this->message->user->name;

        $content = match ($this->message->type) {
            'inquiry' => "sent you a new job inquiry",
            'quote' => "sent you a professional quote",
            'handshake' => "confirmed the deal! Job started.",
            'completion' => "marked the job as completed!",
            default => $this->message->content ? substr($this->message->content, 0, 100) : "sent you a new message",
        };

        $oneSignal->sendToUser(
            $notifiable->onesignal_player_id,
            "Allsers: $senderName",
            $content,
            route('chat', $this->message->conversation_id),
            [
                'type' => 'message',
                'sender_id' => $this->message->user_id,
                'conversation_id' => $this->message->conversation_id,
            ]
        );
    }

    public function toArray(object $notifiable): array
    {
        $text = match ($this->message->type) {
            'inquiry' => "sent you a new job inquiry",
            'quote' => "sent you a professional quote",
            'handshake' => "confirmed the deal! Job started.",
            'completion' => "marked the job as completed!",
            default => 'sent you a new message: ' . substr($this->message->content, 0, 50) . '...',
        };

        return [
            'message_id' => $this->message->id,
            'sender_id' => $this->message->user_id,
            'sender_name' => $this->message->user->name,
            'content' => $this->message->content,
            'conversation_id' => $this->message->conversation_id,
            'message' => $text,
            'type' => 'message',
        ];
    }
}
