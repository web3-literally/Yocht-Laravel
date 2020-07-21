<?php

namespace App\Mail\Boats;

use App\Models\Vessels\Vessel;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\User;

class MemberAssigned extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var Vessel
     */
    protected $boat;

    /**
     * @var User
     */
    protected $member;

    /**
     * Create a new message instance.
     *
     * @param Vessel $boat
     * @param User $member
     * @return void
     */
    public function __construct(Vessel $boat, User $member)
    {
        $this->boat = $boat;
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
            ->subject('You was assigned to ' . $this->boat->name)
            ->markdown('emails.emailTemplates.boats.assigned', [
                'boat' => $this->boat,
                'member' => $this->member
            ]);

        return $this;
    }
}
