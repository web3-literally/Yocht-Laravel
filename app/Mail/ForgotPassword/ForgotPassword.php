<?php

namespace App\Mail\ForgotPassword;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\User;
use Reminder;

class ForgotPassword extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var User
     */
    protected $user;

    /**
     * Create a new message instance.
     *
     * @param User $user
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $reminder = Reminder::exists($this->user) ?: Reminder::create($this->user);
        $forgotPasswordUrl = route('forgot-password-confirm', [$this->user->id, $reminder->code]);

        $this->from(config('mail.from.address'), config('mail.from.name'))
            ->to($this->user->email, $this->user->full_name)
            ->subject('Restore Your ' . config('app.name') . ' Account')
            ->markdown('emails.emailTemplates.signup.forgot-password', [
                'user' => $this->user,
                'userName' => $this->user->full_name,
                'forgotPasswordUrl' => $forgotPasswordUrl,
            ]);

        return $this;
    }
}
