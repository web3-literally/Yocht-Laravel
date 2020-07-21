<?php

namespace App\Mail\Member\Reminder;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentInfoReminder extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * @var User
     */
    protected $member;

    /**
     * Create a new message instance.
     *
     * @param User $member
     * @return void
     */
    public function __construct(User $member)
    {
        $this->member = $member;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->from(config('mail.from.address'), config('mail.from.name'))
            ->to($this->member->email, $this->member->full_name)
            ->subject('Your account on ' . config('app.name') . ' will be inactive')
            ->markdown('emails.emailTemplates.member.payment-info-reminder', [
                'user' => $this->member->email,
                'userName' => $this->member->full_name,
                'member' => $this->member,
            ]);

        return $this;
    }
}
