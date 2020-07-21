<?php

namespace App\Mail\Boats\Transfer;

use App\Models\Vessels\VesselTransfer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OriginConfirmTransfer extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * @var VesselTransfer
     */
    protected $transfer;

    /**
     * @var string
     */
    protected $key;

    /**
     * Create a new message instance.
     *
     * @param VesselTransfer $transfer
     * @param string $key
     * @return void
     */
    public function __construct(VesselTransfer $transfer, $key)
    {
        $this->transfer = $transfer;
        $this->key = $key;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->from(config('mail.from.address'), config('mail.from.name'))
            ->to($this->transfer->origin->email, $this->transfer->origin->full_name)
            ->subject('Transfer ' . $this->transfer->boat->name . ' confirmation')
            ->markdown('emails.emailTemplates.boats.transfer.origin_confirm', [
                'transfer' => $this->transfer,
                'boat' => $this->transfer->boat,
                'origin' => $this->transfer->origin,
                'destination' => $this->transfer->destination,
                'key' => $this->key
            ]);

        return $this;
    }
}
