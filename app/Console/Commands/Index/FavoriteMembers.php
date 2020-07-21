<?php

namespace App\Console\Commands\Index;

use App\Models\Members\FavoriteMember;
use Illuminate\Console\Command;

class FavoriteMembers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'index:favorite-members';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Index favorite members';

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
            FavoriteMember::deleteIndex();
        } catch (\Throwable $e) {
            // Index doesn't exists
        } finally {
            FavoriteMember::createIndex();
        }

        FavoriteMember::putMapping();

        FavoriteMember::addAllToIndex();
    }
}
