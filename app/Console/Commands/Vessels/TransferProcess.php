<?php

namespace App\Console\Commands\Vessels;

use App\Jobs\Vessels\VesselTransferJob;
use App\Models\Vessels\VesselTransfer;
use Carbon\Carbon;
use DB;
use Illuminate\Console\Command;

class TransferProcess extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vessel-transfer:process';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process vessel transfer';

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
        //TODO: Optimize - avoid duplicate jobs
        $processingIds = []; // Avoid duplicate jobs
        $jobs = DB::table(config('queue.connections.database.table'))->where('payload', 'like', '%VesselTransferJob%')->get();
        if ($jobs->count()) {
            foreach($jobs as $job) {
                $payload = json_decode($job->payload);
                $command = unserialize($payload->data->command);
                $processingIds[] = $command->getTransfer()->id;
            }
        }

        VesselTransfer::whereNotIn('id', $processingIds)
            ->where('status', 'pending')
            ->where('origin_confirmed', 1)
            ->where('destination_confirmed', 1)
            ->whereDate('transfer_date', '<=', Carbon::today()->toDateString())
            ->get()
            ->each(function ($transfer) {
                /** @var VesselTransfer $transfer */
                VesselTransferJob::dispatch($transfer)
                    ->onQueue('default');
            });
    }
}
