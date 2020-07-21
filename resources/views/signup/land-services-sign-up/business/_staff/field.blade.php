<div class="staff-field data-field">
    <div class="template" style="display:none">
        @component('signup.land-services-sign-up.business._staff.field-row')
        @endcomponent
    </div>
    <div class="data-rows">
        @foreach($staff as $index => $item)
            @component('signup.land-services-sign-up.business._staff.field-row', ['business_index' => $business_index ?? null])
                @slot('index')
                    {{ $index }}
                @endslot
                @slot('name')
                    {{ $item['name'] }}
                @endslot
                @slot('phone')
                    {{ $item['phone'] }}
                @endslot
                @slot('email')
                    {{ $item['email'] }}
                @endslot
                @slot('type')
                    {{ $item['type'] }}
                @endslot
            @endcomponent
        @endforeach
    </div>
    <div class="actions mt-0 text-center">
        <div class="dropdown">
            <button class="btn btn-secondary dropdown-toggle btn--orange" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                @lang('button.add')
            </button>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <button type="button" class="dropdown-item data-row-add" data-type="manager">Manager</button>
                <button type="button" class="dropdown-item data-row-add" data-type="salesman">Salesman</button>
            </div>
        </div>
    </div>
</div>