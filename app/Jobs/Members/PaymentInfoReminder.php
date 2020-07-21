<?php

namespace App\Jobs\Members;

use App\User;
use Mail;
use App\Mail\Member\Reminder\PaymentInfoReminder as PaymentInfoReminderMail;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

/**
 * Class ClassifiedsLifeReminder
 * @package App\Jobs
 */
class PaymentInfoReminder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
        $usersTable = User::getModel()->getTable();
        $collection = User::members(['owner', 'marine'])->whereBetween($usersTable . '.created_at', [date('Y-m-d', strtotime('-26 days')), date('Y-m-d', strtotime('-25 days'))])
            ->get();

        $collection->each(function ($user, $key) {
            /** @var User $user */
            $paymentMethods = $user->asBraintreeCustomer()->paymentMethods;
            if (count($paymentMethods) == 0 || !$user->hasMembership()) {
                Mail::send(new PaymentInfoReminderMail($user));
            }
        });
    }
}
