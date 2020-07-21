<?php

namespace App\Mail\Manufacturers;

use App\Models\Classifieds\ClassifiedsManufacturer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ApproveBoatManufacturer extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * @var ClassifiedsManufacturer
     */
    protected $manufacturer;

    /**
     * Create a new message instance.
     *
     * @param ClassifiedsManufacturer $manufacturer
     * @return void
     */
    public function __construct(ClassifiedsManufacturer $manufacturer)
    {
        $this->manufacturer = $manufacturer;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->from(config('mail.from.address'), config('mail.from.name'))
            ->to(config('mail.support.address'), config('mail.support.name'))
            ->subject('Posted a new manufacturer on ' . config('app.name'))
            ->markdown('emails.emailTemplates.manufacturers.boat-approve', [
                'manufacturer' => $this->manufacturer,
                'approveUrl' => route('manufacturers.boat.set-status', ['id' => $this->manufacturer->id, 'status' => 'approved', 'key' => sha1(implode('-', [config('manufacturers.secret'), $this->manufacturer->id, 'approved']))]),
                'declineUrl' => route('manufacturers.boat.set-status', ['id' => $this->manufacturer->id, 'status' => 'declined', 'key' => sha1(implode('-', [config('manufacturers.secret'), $this->manufacturer->id, 'declined']))]),
            ]);

        return $this;
    }
}
