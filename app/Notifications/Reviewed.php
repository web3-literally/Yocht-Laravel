<?php

namespace App\Notifications;

use App\Mail\Reviews\ApproveReview;
use App\Models\Reviews\Review;
use App\Notifications\Channels\MemberChannel;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class Reviewed extends Notification
{
    use Queueable;

    /**
     * @var Review
     */
    protected $review;

    /**
     * @var User
     */
    protected $by;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Review $review, User $by)
    {
        $this->review = $review;
        $this->by = $by;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage|ApproveReview
     */
    public function toMail($notifiable)
    {
        return new ApproveReview($this->review);
        /*
            ->greeting('Posted a new review on ' . config('app.name'))
            ->line('Was posted a new review. Please, click button below to approve or decline it.')
            ->action('Approve', '#')
            ->action('Decline', '#')
            ->line('This is a system notification.');*/
    }
}
