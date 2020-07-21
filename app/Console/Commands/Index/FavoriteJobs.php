<?php

namespace App\Console\Commands\Index;

use App\Models\Jobs\FavoriteJob;
use Illuminate\Console\Command;

class FavoriteJobs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'index:favorite-jobs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Index favorite jobs';

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
            FavoriteJob::deleteIndex();
        } catch (\Throwable $e) {
            // Index doesn't exists
        } finally {
            FavoriteJob::createIndex();
        }

        FavoriteJob::putMapping();

        FavoriteJob::addAllToIndex();
    }
}
