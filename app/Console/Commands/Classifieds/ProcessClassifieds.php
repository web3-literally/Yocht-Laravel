<?php

namespace App\Console\Commands\Classifieds;


use App\Models\Classifieds\Classifieds;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use DB;

class ProcessClassifieds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'сlassifieds:process';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process Classifieds';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        /** @var Collection $collection */
        $collection = Classifieds::published()->whereDate('expired_at', '<=', Carbon::now())->get();

        $collection->each(function ($item, $key) {
            DB::beginTransaction();
            try {
                /** @var Classifieds $item */
                $item->status = 'archived';
                $item->can_refresh = 0;

                $item->saveOrFail();

                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();

                report($e);
            }
        });
    }
}
