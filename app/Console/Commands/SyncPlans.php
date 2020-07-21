<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Braintree_Plan;
use App\Plan;

class SyncPlans extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'braintree:sync-plans';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync with online plans on Braintree';

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
        $activePlans = [];

        $braintreePlans = Braintree_Plan::all();

        foreach ($braintreePlans as $braintreePlan) {
            $plan = Plan::where('braintree_plan', '=', $braintreePlan->id)->first();
            if (is_null($plan)) {
                $plan = new Plan();
            }

            $plan->name = $braintreePlan->name;
            if (!$plan->exists) {
                $slug = str_slug($braintreePlan->name);
                if (!is_null(Plan::where('slug', '=', $slug)->first())) {
                    $slug = str_slug($braintreePlan->id);
                }
                $plan->slug = $slug;
            }
            $plan->active = 1;
            $plan->braintree_plan = $braintreePlan->id;
            $plan->cost = $braintreePlan->price;
            $plan->currency = $braintreePlan->currencyIsoCode;
            $plan->billing_frequency = $braintreePlan->billingFrequency;
            $plan->created_at = $braintreePlan->createdAt->getTimestamp();
            $plan->updated_at = $braintreePlan->updatedAt->getTimestamp();

            $plan->save();

            $activePlans[] = $plan->id;
        }

        Plan::whereNotIn('id', $activePlans)->update(['active' => 0]);
    }
}
