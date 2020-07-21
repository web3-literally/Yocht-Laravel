<?php

namespace App\Mail\Member\Reminder;

use App\ExtraOffer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ExtraOfferReminder extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * @var ExtraOffer
     */
    protected $extraOffer;

    /**
     * Create a new message instance.
     *
     * @param ExtraOffer $extraOffer
     * @return void
     */
    public function __construct(ExtraOffer $extraOffer)
    {
        $this->extraOffer = $extraOffer;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->from(config('mail.from.address'), config('mail.from.name'))
            ->to($this->extraOffer->user->email, $this->extraOffer->user->full_name)
            ->subject('Your ' . $this->extraOffer->name . ' offer period ends')
            ->markdown('emails.emailTemplates.member.extra-offer-reminder', [
                'user' => $this->extraOffer->user,
                'userName' => $this->extraOffer->user->full_name,
                'extraOffer' => $this->extraOffer,
            ]);

        return $this;
    }
}
