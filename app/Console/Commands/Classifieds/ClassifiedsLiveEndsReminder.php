<?php

namespace App\Console\Commands\Classifieds;


use Illuminate\Console\Command;

class ClassifiedsLiveEndsReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminder:classifieds:process';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminder if classifieds life time ends soon';

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws \Throwable
     */
    public function handle()
    {
        \App\Jobs\ClassifiedsLifeReminder::dispatch()
            ->onQueue('default');
    }
}
