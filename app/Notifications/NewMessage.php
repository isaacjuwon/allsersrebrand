<?php

namespace App\Notifications;

use App\Models\Message;
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
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message_id' => $this->message->id,
            'sender_id' => $this->message->user_id,
            'sender_name' => $this->message->user->name,
            'content' => $this->message->content,
            'conversation_id' => $this->message->conversation_id,
            'message' => 'sent you a new message: ' . substr($this->message->content, 0, 50) . '...',
            'type' => 'message',
        ];
    }
}
