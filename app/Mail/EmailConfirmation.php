<?php

namespace App\Mail;

use App\EmailConfirmations;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\User;

class EmailConfirmation extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * @var User
     */
    protected $user;

    /**
     * @var EmailConfirmations
     */
    protected $confirmation;

    /**
     * Create a new message instance.
     *
     * @param User $user
     * @param EmailConfirmations $confirmation
     * @return void
     */
    public function __construct(User $user, EmailConfirmations $confirmation)
    {
        $this->user = $user;
        $this->confirmation = $confirmation;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->from(config('mail.from.address'), config('mail.from.name'))
            ->to($this->confirmation->email, $this->user->full_name)
            ->subject('Change email confirmation on ' . config('app.name'))
            ->markdown('emails.emailTemplates.email-confirmation', [
                'user' => $this->user,
                'userName' => $this->user->full_name,
                'confirmation' => $this->confirmation,
                'confirmationLink' => route('email-confirmation', [
                    'userId' => $this->user->id,
                    'confirmationCode' => $this->confirmation->getCode()
                ], true)
            ]);

        return $this;
    }
}
