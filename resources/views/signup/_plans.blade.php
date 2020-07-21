<div class="plans">
    @if($plans)
        @foreach($plans as $plan)
            <span class="plan plan-{{ $loop->index + 1 }}">
                <span class="cost">
                    <span class="currency">{{ PlanHelper::getCurrencyLabel($plan->currency) }}</span>
                    <span class="value">{{ number_format($plan->cost, 0) }}</span>
                </span>
                <span class="frequency"><span class="divider">/</span>{{ PlanHelper::getFrequencyLabel($plan->billing_frequency) }}</span>
            </span>
        @endforeach
    @endif
</div>