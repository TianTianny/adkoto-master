<?php

namespace App\Notifications;

use App\Models\Post;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CommentReplied extends Notification
{
    use Queueable;

    public User $commenter;
    public Post $post;
    public string $comment;
    public object $parentComment;

    /**
     * Create a new notification instance.
     */
    public function __construct($commenter, $post, $comment, $parentComment)
    {
        $this->commenter = $commenter;
        $this->post = $post;
        $this->comment = $comment;
        $this->parentComment = $parentComment;
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
            // 'post_id' => $this->post->id,
            'user_id' => $this->commenter->id,
            'username' => $this->commenter->username,
            'post_id' => $this->post->id,
            'post_title' => $this->post->title,
            'comment' => $this->comment,
            'route' => route('post.view', $this->post->id),
            'message' => $this->commenter->name . ' replied to your comment: ' . $this->parentComment->comment,
        ];
    }
}