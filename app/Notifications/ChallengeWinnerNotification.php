<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ChallengeWinnerNotification extends Notification
{
    use Queueable;

    public $challenge;

    /**
     * Create a new notification instance.
     */
    public function __construct($challenge)
    {
        $this->challenge = $challenge;
    }

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
        return (new MailMessage)
            ->subject('Congratulations! You won the challenge!')
            ->line('We are thrilled to announce that you have been selected as the winner of: ' . $this->challenge->title)
            ->line('A unique badge has been added to your profile.')
            ->action('View Your Profile', route('artisan.profile', auth()->user()->username ?? ''))
            ->line('Keep up the amazing work!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'challenge_id' => $this->challenge->id,
            'title' => 'Challenge Winner!',
            'message' => 'Congratulations! You won the ' . $this->challenge->title . ' challenge.',
            'link' => route('artisan.profile', auth()->user()->username ?? ''),
            'type' => 'challenge_winner'
        ];
    }
}
