<?php

namespace App\Console\Commands\Index;

use App\Models\Reviews\Review;
use Illuminate\Console\Command;

class Reviews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'index:reviews';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Index searchable reviews';

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
            Review::deleteIndex();
        } catch (\Throwable $e) {
            // Index doesn't exists
        } finally {
            Review::createIndex();
        }

        Review::putMapping();

        Review::searchable()->get()->each(function ($item, $key) {
            $item->addToIndex();
        });
    }
}
