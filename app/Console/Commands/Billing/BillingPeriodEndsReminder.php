<?php

namespace App\Console\Commands\Billing;


use Illuminate\Console\Command;

class BillingPeriodEndsReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminder:billing:process';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminder if subscription or offer ends soon';

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws \Throwable
     */
    public function handle()
    {
        \App\Jobs\SubscriptionReminder::dispatch()
            ->onQueue('default');

        \App\Jobs\ExtraOfferReminder::dispatch()
            ->onQueue('default');
    }
}
