<?php

namespace App\Mail\Member\Reminder;

use App\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SubscriptionReminder extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * @var Subscription
     */
    protected $subscription;

    /**
     * Create a new message instance.
     *
     * @param Subscription $subscription
     * @return void
     */
    public function __construct(Subscription $subscription)
    {
        $this->subscription = $subscription;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->from(config('mail.from.address'), config('mail.from.name'))
            ->to($this->subscription->user->email, $this->subscription->user->full_name)
            ->subject('Your ' . config('app.name') . ' Membership period ends')
            ->markdown('emails.emailTemplates.member.subscription-reminder', [
                'user' => $this->subscription->user,
                'userName' => $this->subscription->user->full_name,
                'subscription' => $this->subscription,
            ]);

        return $this;
    }
}
