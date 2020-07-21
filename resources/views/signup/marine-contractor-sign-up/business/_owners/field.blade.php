<div class="owners-field data-field">
    <div class="template" style="display:none">
        @component('signup.marine-contractor-sign-up.business._owners.field-row')
        @endcomponent
    </div>
    <div class="data-rows">
        @foreach($owners as $index => $owner)
            @component('signup.marine-contractor-sign-up.business._owners.field-row', ['business_index' => $business_index ?? null])
                @slot('index')
                    {{ $index }}
                @endslot
                @slot('email')
                    {{ $owner['email'] }}
                @endslot
                @slot('first_name')
                    {{ $owner['first_name'] }}
                @endslot
                @slot('last_name')
                    {{ $owner['last_name'] }}
                @endslot
                @slot('phone')
                    {{ $owner['phone'] }}
                @endslot
            @endcomponent
        @endforeach
    </div>
    <div class="actions mt-0 text-center">
        <button type="button" class="btn data-row-add btn--orange">@lang('button.add')</button>
    </div>
</div>