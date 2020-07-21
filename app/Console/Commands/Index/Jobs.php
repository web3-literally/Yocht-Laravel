<?php

namespace App\Console\Commands\Index;

use App\Models\Jobs\Job as Model;
use Illuminate\Console\Command;

class Jobs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'index:jobs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Index jobs';

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
        try {
            Model::deleteIndex();
        } catch (\Throwable $e) {
            // Index doesn't exists
        } finally {
            Model::createIndex();
        }

        Model::putMapping();

        Model::addAllToIndex();
    }
}
