<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Mail\Member\Reminder\SubscriptionReminder as SubscriptionReminderMail;
use Braintree\Subscription;
use Illuminate\Database\Eloquent\Builder;
use Mail;
use DB;

/**
 * Class SubscriptionReminder
 * @package App\Jobs
 */
class SubscriptionReminder implements ShouldQueue
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
        $daysFromNow = new \DateTime();
        $daysFromNow->modify('+' . self::INTERVAL_DAYS . ' days');

        $collection = Subscription::search([
            \Braintree_SubscriptionSearch::status()->is(\Braintree_Subscription::ACTIVE),
            \Braintree_SubscriptionSearch::nextBillingDate()->lessThanOrEqualTo($daysFromNow)
        ]);

        foreach ($collection as $item) {
            DB::beginTransaction();
            try {
                $nextBillingDate = $item->nextBillingDate->format('Y-m-d H:i:s');
                $subscription = \App\Subscription::where('braintree_id', $item->id)->where(function (Builder $query) use ($nextBillingDate) {
                    $query->whereNull('ends_soon_reminder_at')
                        ->orWhere('ends_soon_reminder_at', '<', $nextBillingDate);
                })->first();
                if ($subscription) {
                    $subscription->ends_soon_reminder_at = $nextBillingDate;
                    $subscription->saveOrFail();

                    Mail::send(new SubscriptionReminderMail($subscription));
                }
                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();

                report($e);
            }
        }
    }
}
