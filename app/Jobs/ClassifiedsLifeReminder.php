<?php

namespace App\Jobs;

use App\Models\Classifieds\Classifieds;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Mail\Member\Reminder\ClassifiedLifeReminder as ClassifiedLifeReminderMail;
use Illuminate\Database\Eloquent\Builder;
use Mail;
use DB;

/**
 * Class ClassifiedsLifeReminder
 * @package App\Jobs
 */
class ClassifiedsLifeReminder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    const INTERVAL_DAYS = 5;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws \Throwable
     */
    public function handle()
    {
        $daysFromNow = new \DateTime();
        $daysFromNow->modify('+' . self::INTERVAL_DAYS . ' days');

        $collection = Classifieds::published()->where(function (Builder $query) use ($daysFromNow) {
            $query->whereNull('expired_at')
                ->orWhere('expired_at', '<', $daysFromNow);
        })->get();

        /** @var Classifieds $classified */
        foreach ($collection as $classified) {
            DB::beginTransaction();
            try {
                DB::table($classified->getTable())->where('id', $classified->id)->update(['can_refresh' => 1]);

                Mail::send(new ClassifiedLifeReminderMail($classified));

                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();

                report($e);
            }
        }
    }
}
