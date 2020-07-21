<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Mail\Member\Reminder\ExtraOfferReminder as ExtraOfferReminderMail;
use App\ExtraOffer;
use Illuminate\Database\Eloquent\Builder;
use Mail;
use DB;

/**
 * Class ExtraOfferReminder
 * @package App\Jobs
 */
class ExtraOfferReminder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    const INTERVAL_DAYS = 3;

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
        $collection = ExtraOffer::where('finished_at', '<=', DB::raw('CURDATE() + INTERVAL ' . self::INTERVAL_DAYS . ' DAY'))
            ->where(function (Builder $query) {
                $query->whereNull('ends_soon_reminder_at')
                    ->orWhere('ends_soon_reminder_at', '<', DB::raw('finished_at'));
            })->get();

        foreach ($collection as $extraOffer) {
            DB::beginTransaction();
            try {
                $extraOffer->ends_soon_reminder_at = $extraOffer->finished_at;
                $extraOffer->saveOrFail();

                Mail::send(new ExtraOfferReminderMail($extraOffer));

                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();

                report($e);
            }
        }
    }
}
