<?php

namespace App\Console\Commands\Index;

use App\User;
use Illuminate\Console\Command;

class Members extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'index:members';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Index searchable members';

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
            User::deleteIndex();
        } catch (\Throwable $e) {
            // Index doesn't exists
        } finally {
            User::createIndex();
        }

        User::putMapping();

        User::searchableAccounts()->get()->each(function ($item, $key) {
            $item->addToIndex();
        });
    }
}
