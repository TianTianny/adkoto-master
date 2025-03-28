<?php

namespace App\Notifications;

use App\Models\Post;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PostReacted extends Notification
{
    use Queueable;

    public User $user;
    public Post $post;
    public string $reaction;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $user, Post $post, string $reaction)
    {
        $this->user = $user;
        $this->post = $post;
        $this->reaction = $reaction;
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
    // public function toArray(object $notifiable): array
    // {
    //     return [
    //
    //     ];
    // }
    public function toDatabase(object $notifiable): array
    {
        return [
            'user_id' => $this->user->id,
            'username' => $this->user->username,
            'post_id' => $this->post->id,
            'post_title' => $this->post->title,
            'reaction' => $this->reaction,
            'route' => route('post.view', $this->post->id),
            'message' => $this->user->name . ' ' . $this->user->surname . ' reacted to your post',
            'reaction_image' => $this->getReaction()
        ];
    }

    /**
     * Get the notification message based on reaction.
     *
     * @return string
     */
    private function getReaction(): string
    {
        // $emojis = [
        //     'like' => '👍',
        //     'love' => '❤️',
        //     'haha' => '😂',
        //     'wow' => '😮',
        //     'sad' => '😢',
        //     'angry' => '😡',
        // ];

        $emojis = [
            'like' => asset('img/Reactions/like.png'),
            'love' => asset('img/Reactions/love.png'),
            'haha' => asset('img/Reactions/haha.png'),
            'wow' => asset('img/Reactions/wow.png'),
            'sad' => asset('img/Reactions/sad.png'),
            'angry' => asset('img/Reactions/angry.png'),
        ];

        // $emoji = $emojis[$this->reaction] ?? '❓';
        // $emoji = $emojis[$this->reaction] ?? asset('img/Reactions/like.png');

        // return "{$this->user->name} {$this->user->surname} reacted {$emoji} to your post";
        return $emojis[$this->reaction] ?? asset('images/reactions/default.png');
    }
}
