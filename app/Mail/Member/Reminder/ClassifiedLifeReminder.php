<?php

namespace App\Mail\Member\Reminder;

use App\Models\Classifieds\Classifieds;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ClassifiedLifeReminder extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * @var Classifieds
     */
    protected $classified;

    /**
     * Create a new message instance.
     *
     * @param Classifieds $classified
     * @return void
     */
    public function __construct(Classifieds $classified)
    {
        $this->classified = $classified;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->from(config('mail.from.address'), config('mail.from.name'))
            ->to($this->classified->refresh_email, '')
            ->subject('Refresh your classified on ' . config('app.name'))
            ->markdown('emails.emailTemplates.member.classified-life-reminder', [
                'user' => $this->classified->user,
                'userName' => '',
                'refreshUrl' => route('classifieds.refresh', ['id' => $this->classified->id, 'key' => sha1(implode('-', [config('classifieds.secret'), $this->classified->id, 'refresh']))]),
                'deactivateUrl' => route('classifieds.deactivate', ['id' => $this->classified->id, 'key' => sha1(implode('-', [config('classifieds.secret'), $this->classified->id, 'deactivate']))]),
                'classified' => $this->classified,
            ]);

        return $this;
    }
}
