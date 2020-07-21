<?php

namespace App\Notifications;

use App\Models\Jobs\Job;
use App\Models\Reviews\Review;
use App\Notifications\Channels\MemberChannel;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class JobReviewed extends Notification
{
    use Queueable;

    /**
     * @var Job
     */
    protected $job;

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
    public function __construct(Review $review, Job $job, User $by)
    {
        $this->job = $job;
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
        return [MemberChannel::class];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        return [
            'instance_id' => $this->review->id,
            'id' => $this->job->id,
            'title' => $this->job->title,
            'by_id' => $this->by->getUserId(),
            'by_title' => $this->by->member_title,
        ];
    }
}
