<div class="container-fluid data-row mb-3" {!! isset($index) ? 'data-index="' . $index . '"' : '' !!}>
    <div class="row">
        <div class="col-4 {{ isset($index) ? $errors->first('business.'.$business_index.'.staff.' . $index . '.name', 'has-error') : null }}">
            @php
                $userType = '[user-type]';
                if (isset($type) && $type == 'manager') {
                    $userType = 'Manager';
                }
                if (isset($type) && $type == 'salesman') {
                    $userType = 'Salesman';
                }
            @endphp
            <input type="text" data-name="business[*b*][staff][*i*][name]" {!! isset($index) ? 'name="business['.$business_index.'][staff][' . $index . '][name]"' : '' !!} autocomplete="off" class="form-control data-name" value="{{ $name ?? '' }}" placeholder="{{ $userType }} Name*">
            @if(isset($index))
                {!! $errors->first('business.'.$business_index.'.staff.' . $index . '.name', '<span class="help-block">:message</span>') !!}
            @endif
        </div>
        <div class="col-3 {{ isset($index) ? $errors->first('business.'.$business_index.'.staff.' . $index . '.phone', 'has-error') : null }}">
            <input type="text" data-name="business[*b*][staff][*i*][phone]" {!! isset($index) ? 'name="business['.$business_index.'][staff][' . $index . '][phone]"' : '' !!} autocomplete="off" class="form-control data-phone" value="{{ $phone ?? '' }}" placeholder="Phone">
            @if(isset($index))
                {!! $errors->first('business.'.$business_index.'.staff.' . $index . '.phone', '<span class="help-block">:message</span>') !!}
            @endif
        </div>
        <div class="col-3 {{ isset($index) ? $errors->first('business.'.$business_index.'.staff.' . $index . '.email', 'has-error') : null }}">
            <input type="text" data-name="business[*b*][staff][*i*][email]" {!! isset($index) ? 'name="business['.$business_index.'][staff][' . $index . '][email]"' : '' !!} autocomplete="off" class="form-control data-email" value="{{ $email ?? '' }}" placeholder="Email">
            @if(isset($index))
                {!! $errors->first('business.'.$business_index.'.staff.' . $index . '.email', '<span class="help-block">:message</span>') !!}
            @endif
        </div>
        <input type="hidden" class="data-type" data-name="business[*b*][staff][*i*][type]" {!! isset($index) ? 'name="business['.$business_index.'][staff][' . $index . '][type]"' : '' !!} value="{{ $type ?? '' }}">
        <div class="col-2">
            <button class="btn data-row-delete" type="button">
                @lang('button.delete')
            </button>
        </div>
    </div>
</div>