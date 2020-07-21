<?php

namespace App\Mail\Member;

use App\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Canceled extends Mailable
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
            ->subject('Your ' . config('app.name') . ' Membership canceled')
            ->markdown('emails.emailTemplates.member.canceled', [
                'user' => $this->subscription->user,
                'userName' => $this->subscription->user->full_name,
                'subscription' => $this->subscription,
                'force' => !$this->subscription->active(),
            ]);

        return $this;
    }
}
