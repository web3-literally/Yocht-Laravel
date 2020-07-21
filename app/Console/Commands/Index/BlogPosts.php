<?php

namespace App\Console\Commands\Index;

use App\Blog as Model;
use Illuminate\Console\Command;

class BlogPosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'index:blog-posts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Index blog posts';

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
