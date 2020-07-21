@if(Sentinel::getUser()->isMemberAccount())
    @if(!Sentinel::getUser()->hasMembership())
        <div class="alert alert-danger">
            @if(\App\Subscription::my()->get()->count())
                @lang('subscriptions.your_subscription_has_expired', ['link' => '<a href="'.route('subscription.plans').'">link</a>'])
            @else
                @lang('subscriptions.you_havent_subscribed_yet', ['link' => '<a href="'.route('subscription.plans').'">link</a>'])
            @endif
        </div>
    @endif
@endif