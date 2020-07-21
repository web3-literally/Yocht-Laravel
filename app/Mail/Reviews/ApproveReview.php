<?php

namespace App\Mail\Reviews;

use App\Models\Reviews\Review;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ApproveReview extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * @var Review
     */
    protected $review;

    /**
     * Create a new message instance.
     *
     * @param Review $review
     * @return void
     */
    public function __construct(Review $review)
    {
        $this->review = $review;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->from(config('mail.from.address'), config('mail.from.name'))
            ->to(config('mail.support.address'), config('mail.support.name'))
            ->subject('Posted a new review on ' . config('app.name'))
            ->markdown('emails.emailTemplates.reviews.approve', [
                'review' => $this->review,
                'approveUrl' => route('reviews.set-status', ['id' => $this->review->id, 'status' => 'approved', 'key' => sha1(implode('-', [config('reviews.secret'), $this->review->id, 'approved']))]),
                'declineUrl' => route('reviews.set-status', ['id' => $this->review->id, 'status' => 'declined', 'key' => sha1(implode('-', [config('reviews.secret'), $this->review->id, 'declined']))]),
            ]);

        return $this;
    }
}
