<?php

namespace App\Mail\Boats\Transfer;

use App\Models\Vessels\Vessel;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Transferred extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * @var Vessel
     */
    protected $boat;

    /**
     * @var User
     */
    protected $memberFrom;

    /**
     * @var User
     */
    protected $memberTo;

    /**
     * Create a new message instance.
     *
     * @param Vessel $boat
     * @param User $from
     * @param User $to
     * @return void
     */
    public function __construct(Vessel $boat, User $from, User $to)
    {
        $this->boat = $boat;
        $this->memberFrom = $from;
        $this->memberTo = $to;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->from(config('mail.from.address'), config('mail.from.name'))
            ->to($this->memberTo->email, $this->memberTo->full_name)
            ->subject($this->boat->name . ' was transferred to your account')
            ->markdown('emails.emailTemplates.boats.transfer.transferred', [
                'boat' => $this->boat,
                'from' => $this->memberFrom,
                'to' => $this->memberTo
            ]);

        return $this;
    }
}
