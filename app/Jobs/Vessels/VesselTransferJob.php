<?php

namespace App\Jobs\Vessels;

use App\Mail\Boats\Transfer\Transferred;
use App\Models\Vessels\VesselTransfer;
use DB;
use Exception;
use Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

/**
 * Class VesselTransferJob
 * @package App\Jobs
 */
class VesselTransferJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var VesselTransfer
     */
    protected $transfer;

    /**
     * Create a new job instance.
     *
     * @param VesselTransfer $transfer
     * @return void
     */
    public function __construct(VesselTransfer $transfer)
    {
        $this->transfer = $transfer;
    }

    /**
     * @return VesselTransfer
     */
    public function getTransfer()
    {
        return $this->transfer;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws \Throwable
     */
    public function handle()
    {
        $transfer = $this->transfer;

        DB::beginTransaction();
        try {
            $data = $transfer->data;
            $boat = $transfer->boat;
            $holder = $transfer->origin;
            $member = $transfer->destination;

            $transfer->status = 'complete';
            $transfer->saveOrFail();

            if ($boat->type == 'tender') {
                $boat->parent_id = null;
                $boat->user_id = $member->id;
            }
            $boat->owner_id = $member->id;
            if ($member->primaryVessel) {
                $boat->is_primary = 0;
            }
            $boat->saveOrFail();

            $boat->user->parent_id = $member->id;
            $boat->user->saveOrFail();

            // Transfer selected tenders
            $ids = (array)($data['tenders'] ?? []);
            foreach ($boat->tenders as $tender) {
                if (in_array($tender->id, $ids)) {
                    $tender->user_id = $member->id;
                    $tender->owner_id = $member->id;
                    $tender->saveOrFail();
                } else {
                    $tender->parent_id = null;
                    $tender->saveOrFail();
                }
            }

            // Transfer crew
            if ($boat->crew->count()) {
                foreach ($boat->crew as $link) {
                    $link->owner_id = $member->id;
                    $link->saveOrFail();
                }
            }

            Mail::send(new Transferred($boat, $holder, $member));

            DB::commit();

            if ($boat->type == 'vessel') {
                $boat->user->removeFromIndex();
                $boat->user->addToIndex();
            }
        } catch (\Throwable $e) {
            DB::rollback();

            report($e);

            throw new Exception('Failed to transfer vessel', 500, $e);
        }
    }
}
