<?php

namespace App\Console\Commands\Billing;

use App\Exceptions\PaymentException;
use App\Models\ExtraOffers\ExtraVesselOffer;
use App\Repositories\ExtraVesselOfferRepository;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use DB;

class ProcessExtraVesselOffer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'extra-offer:vessel:process';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process Extra Vessel Offer';

    /**
     * @var ExtraVesselOfferRepository
     */
    protected $extraOfferRepository;

    /**
     * Create a new command instance.
     *
     * @param ExtraVesselOfferRepository $extraOfferRepository
     * @return void
     */
    public function __construct(ExtraVesselOfferRepository $extraOfferRepository)
    {
        parent::__construct();

        $this->extraOfferRepository = $extraOfferRepository;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        /** @var Collection $finishedOffers */
        $finishedOffers = ExtraVesselOffer::extraVessel()->active()->whereDate('finished_at', '<=', Carbon::now())->get();

        $finishedOffers->each(function ($item, $key) {
            try {
                /** @var ExtraVesselOffer $item */
                $item->renew();

                DB::commit();
            } catch (PaymentException $e) {
                DB::rollback();

                $item->forceDelete();

                // What we are going to do if paid failed for extra boat?
            } catch (\Throwable $e) {
                DB::rollback();

                report($e);
            }
        });
    }
}
