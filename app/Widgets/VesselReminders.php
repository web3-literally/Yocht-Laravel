<?php

namespace App\Widgets;

use App\Helpers\RelatedProfile;
use App\Repositories\EventsRepository;
use App\Repositories\TicketsRepository;
use App\User;
use Arrilot\Widgets\AbstractWidget;
use App\Models\Events\Event;
use Carbon\Carbon;
use Sentinel;

/**
 * Class VesselReminders
 * @package App\Widgets
 */
class VesselReminders extends AbstractWidget
{
    /**
     * @var EventsRepository
     */
    protected $eventsRepository;

    /**
     * @var TicketsRepository
     */
    protected $ticketsRepository;

    /**
     * @param EventsRepository $eventsRepository
     * @param TicketsRepository $ticketsRepository
     * @param array $config
     */
    public function __construct(EventsRepository $eventsRepository, TicketsRepository $ticketsRepository, array $config = [])
    {
        parent::__construct($config);

        $this->eventsRepository = $eventsRepository;
        $this->ticketsRepository = $ticketsRepository;
    }

    /**
     * The configuration array.
     *
     * @var array
     */
    protected $config = [];

    /**
     * Treat this method as a controller action.
     * Return view() or other content to display.
     */
    public function run()
    {
        /** @var User $user */
        $user = Sentinel::getUser();

        $boat = RelatedProfile::currentVesselMember();

        $events = $this->eventsRepository->getEventsByDate($boat, Carbon::today()); // Related profile events

        $nearest = $this->eventsRepository->getNearestEventsByDate($boat, Carbon::today()); // Nearest events from Related profile
        $events = $events->concat($nearest)->unique();

        $tickets = $this->ticketsRepository->getTicketsByDate($boat, Carbon::today());
        $events = $events->concat($tickets);

        return view('widgets.vessel_reminders', [
            'config' => $this->config,
            'boat_id' => $boat->id,
            'boat' => $boat,
            'events' => $events,
            'user' => $user
        ]);
    }
}
