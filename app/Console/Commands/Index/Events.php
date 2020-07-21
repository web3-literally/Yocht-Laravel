<?php

namespace App\Console\Commands\Index;

use App\Models\Events\Event as Model;
use Illuminate\Console\Command;

class Events extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'index:events';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Index events';

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
