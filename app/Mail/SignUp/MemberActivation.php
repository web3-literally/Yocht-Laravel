<?php

namespace App\Mail\SignUp;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\User;
use Cartalyst\Sentinel\Laravel\Facades\Activation;

class MemberActivation extends Mailable implements ShouldQueue
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
        $this->from(config('mail.from.address'), config('mail.from.name'))
            ->to($this->user->email, $this->user->full_name)
            ->subject('Activate Your ' . config('app.name') . ' Account')
            ->markdown('emails.emailTemplates.signup.activate-member', [
                'user' => $this->user,
                'userName' => $this->user->full_name,
                'activationUrl' => route('activate', [$this->user->id, Activation::create($this->user)->code]),
            ]);

        return $this;
    }
}
