<?php

namespace App\Notifications;

use App\Models\Post;
use App\Models\User;
use App\Services\OneSignalService;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PostLiked extends Notification
{
    use Queueable;

    protected $post;
    protected $liker;

    /**
     * Create a new notification instance.
     */
    public function __construct(Post $post, User $liker)
    {
        $this->post = $post;
        $this->liker = $liker;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        if ($notifiable->onesignal_player_id) {
            $this->sendPushNotification($notifiable);
        }

        return ['database'];
    }

    protected function sendPushNotification($notifiable)
    {
        $oneSignal = app(OneSignalService::class);
        $oneSignal->sendToUser(
            $notifiable->onesignal_player_id,
            "New Like!",
            $this->liker->name . " liked your post",
            route('posts.show', $this->post->post_id),
            [
                'type' => 'like',
                'post_id' => $this->post->id,
                'liker_id' => $this->liker->id,
            ]
        );
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'post_id' => $this->post->id,
            'liker_id' => $this->liker->id,
            'liker_name' => $this->liker->name,
            'message' => 'liked your post',
            'type' => 'like',
        ];
    }
}
