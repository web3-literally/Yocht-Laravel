<?php

namespace App;

use App\Events\Member\Subscription\Canceled;
use Sentinel;
use Laravel\Cashier\Subscription as CashierSubscription;
use Event as AppEvent;

/**
 * Class Subscription
 * @package App
 */
class Subscription extends CashierSubscription
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function plan()
    {
        return $this->belongsTo(Plan::class, 'braintree_plan', 'braintree_plan');
    }

    public function scopeMy($query)
    {
        $user = Sentinel::getUser();
        return $query->orderBy('created_at', 'desc')->where('user_id', $user->getUserId());
    }

    /**
     * @return $this
     */
    public function cancel()
    {
        parent::cancel();
        AppEvent::fire(new Canceled($this));

        return $this;
    }

    /**
     * @return $this|CashierSubscription
     */
    public function cancelNow()
    {
        parent::cancelNow();
        AppEvent::fire(new Canceled($this));

        return $this;
    }

    /**
     * return void
     */
    public function markAsCancelled() {
        $this->cancelled = 1;
        parent::markAsCancelled();
    }
}
