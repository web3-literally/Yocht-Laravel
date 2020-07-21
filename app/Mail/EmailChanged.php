<?php

namespace App\Mail;

use App\EmailConfirmations;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\User;

class EmailChanged extends Mailable implements ShouldQueue
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
     * @var string
     */
    protected $oldEmail;

    /**
     * Create a new message instance.
     *
     * @param User $user
     * @param EmailConfirmations $confirmation
     * @param string $oldEmail
     * @return void
     */
    public function __construct(User $user, EmailConfirmations $confirmation, $oldEmail)
    {
        $this->user = $user;
        $this->confirmation = $confirmation;
        $this->oldEmail = $oldEmail;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->from(config('mail.from.address'), config('mail.from.name'))
            ->to($this->oldEmail, $this->user->full_name)
            ->subject('Your ' . config('app.name') . ' account email has been changed')
            ->markdown('emails.emailTemplates.email-changed', [
                'user' => $this->user,
                'userName' => $this->user->full_name,
                'confirmation' => $this->confirmation,
                'oldEmail' => $this->oldEmail
            ]);

        return $this;
    }
}
