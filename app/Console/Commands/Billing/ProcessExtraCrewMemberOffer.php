<?php

namespace App\Console\Commands\Billing;

use App\Exceptions\PaymentException;
use App\Models\ExtraOffers\ExtraCrewMemberOffer;
use App\Models\Vessels\VesselsCrew;
use App\Repositories\ExtraCrewOfferRepository;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use DB;

class ProcessExtraCrewMemberOffer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'extra-offer:crew-member:process';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process Extra Crew Member Offer';

    /**
     * @var ExtraCrewOfferRepository
     */
    protected $extraOfferRepository;

    /**
     * Create a new command instance.
     *
     * @param ExtraCrewOfferRepository $extraOfferRepository
     * @return void
     */
    public function __construct(ExtraCrewOfferRepository $extraOfferRepository)
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
        $finishedOffers = ExtraCrewMemberOffer::extraTeamMember()->active()->whereDate('finished_at', '<=', Carbon::now())->get();

        $finishedOffers->each(function ($item, $key) {
            try {
                /** @var ExtraCrewMemberOffer $item */
                $item->renew();

                DB::commit();
            } catch (PaymentException $e) {
                DB::rollback();

                $item->setFail();
            } catch (\Throwable $e) {
                DB::rollback();

                report($e);
            }
        });
    }
}
