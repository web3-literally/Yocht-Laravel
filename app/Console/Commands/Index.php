<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Index extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'index:all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Index all';

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
        $this->call('index:members');
        $this->call('index:reviews');
        $this->call('index:favorite-members');
        $this->call('index:blog-posts');
        $this->call('index:events');
        $this->call('index:jobs');
        $this->call('index:favorite-jobs');
        $this->call('index:classifieds');
        $this->call('index:vessel-attachments');
    }
}
