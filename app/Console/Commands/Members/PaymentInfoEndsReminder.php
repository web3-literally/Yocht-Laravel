<?php

namespace App\Console\Commands\Members;

use Illuminate\Console\Command;

class PaymentInfoEndsReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminder:payment-info:process';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminder if period without payment info ends soon';

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws \Throwable
     */
    public function handle()
    {
        \App\Jobs\Members\PaymentInfoReminder::dispatch()
            ->onQueue('default');
    }
}
