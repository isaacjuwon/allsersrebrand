<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ChallengeJudgeInvitation extends Notification
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
            ->subject('You have been invited to judge a challenge!')
            ->line('You have been cordially invited to be a judge for the challenge: ' . $this->challenge->title)
            ->action('View Challenge', route('challenges.show', $this->challenge->custom_link))
            ->line('Thank you for being a vital part of our community!');
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
            'title' => $this->challenge->title,
            'message' => 'You have been invited to judge the ' . $this->challenge->title . ' challenge.',
            'link' => route('challenges.show', $this->challenge->custom_link),
            'type' => 'challenge_invitation'
        ];
    }
}
