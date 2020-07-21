<?php

namespace App\Console\Commands\Events;

use App\Jobs\Index\EventsDelete;
use App\Models\Events\Event;
use Carbon\Carbon;
use DB;
use Illuminate\Console\Command;

class CleanupEvents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'events:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleanup old/past events';

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws \Throwable
     */
    public function handle()
    {
        $rows = Event::whereDate('ends_at', '<', Carbon::now())->get();
        foreach ($rows as $row) {
            DB::beginTransaction();
            try {
                if (!$row->delete()) {
                    throw new \Exception('Failed to delete event #' . $row->id);
                }
                EventsDelete::dispatchNow($row->id);

                DB::commit();

                $this->comment('Event #' . $row->id . ' was deleted');
            } catch (\Throwable $e) {
                DB::rollback();

                report($e);
            }
        }
    }
}
