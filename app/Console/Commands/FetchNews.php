<?php

namespace App\Console\Commands;

use App\Models\NewsSource;
use Illuminate\Console\Command;
use DB;

class FetchNews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'news:fetch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch news from provided sources';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //TODO: Optimize - avoid duplicate jobs
        $processingIds = []; // Avoid duplicate news fetch jobs
        $jobs = DB::table(config('queue.connections.database.table'))->where('payload', 'like', '%FetchNews%')->get();
        if ($jobs->count()) {
            foreach($jobs as $job) {
                $payload = json_decode($job->payload);
                $command = unserialize($payload->data->command);
                $processingIds[] = $command->getSource()->id;
            }
        }

        $sources = NewsSource::whereNotIn('id', $processingIds)->get();

        if ($sources->count()) {
            foreach($sources as $source) {
                \App\Jobs\FetchNews::dispatch($source)
                    ->onQueue('default');
            }
        }
    }
}
