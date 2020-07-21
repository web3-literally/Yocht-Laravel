<?php

namespace App\Events\Vessel;

use App\Models\Vessels\Vessel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class Relocate
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Vessel
     */
    public $vessel;

    /**
     * Create a new event instance.
     *
     * @param Vessel $vessel
     *
     * @return void
     */
    public function __construct(Vessel $vessel)
    {
        $this->vessel = $vessel;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('vessel_relocate.' . $this->vessel->id);
    }
}
