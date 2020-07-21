<?php

namespace App\Console\Commands\Index;

use App\Models\Classifieds\Classifieds as Model;
use Cache;
use Illuminate\Console\Command;

class Classifieds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'index:classifieds';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Index classifieds';

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

        Cache::forget('ClassifiedsCategoriesAll_' . Model::TYPE_BOAT);
        Cache::forget('ClassifiedsCategoriesAll_' . Model::TYPE_PART);
        Cache::forget('ClassifiedsCategoriesAll_' . Model::TYPE_ACCESSORY);

        Cache::forget('ClassifiedsBrandsAll_' . Model::TYPE_BOAT);
        Cache::forget('ClassifiedsBrandsAll_' . Model::TYPE_PART);
        Cache::forget('ClassifiedsBrandsAll_' . Model::TYPE_ACCESSORY);

        Cache::forget('ClassifiedsLocationsAll_' . Model::TYPE_BOAT);
        Cache::forget('ClassifiedsLocationsAll_' . Model::TYPE_PART);
        Cache::forget('ClassifiedsLocationsAll_' . Model::TYPE_ACCESSORY);
    }
}
