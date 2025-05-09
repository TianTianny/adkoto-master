<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AddedToGroupChat extends Notification
{
    use Queueable;
    protected $group;
    protected $user;
    protected $addedUser;

    /**
     * Create a new notification instance.
     */
    public function __construct($group, $user, $addedUser)
    {
        $this->group = $group;
        $this->user = $user;
        $this->addedUser = $addedUser;
    }

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
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }

    /**
     * Get the array representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'group_id' => $this->group->id,
            'group_name' => $this->group->name,
            'user_id' => $this->user->id,
            'added_user_id' => $this->addedUser->id,
            'route' => route('group-chats.index', $this->group->id),
            // 'message' => 'You have been invited to join the group chat "' . $this->group->name . '".',
            'message' => $this->user->name . ' added ' . $this->addedUser->name . ' to the group "' . $this->group->name . '".',
            'icon' => '🎉',
        ];
    }
}
