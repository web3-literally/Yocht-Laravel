<div class="container-fluid data-row mb-3" {!! isset($index) ? 'data-index="' . $index . '"' : '' !!}>
    <div class="row">
        <div class="col-2 {{ isset($index) ? $errors->first('captains.' . $index . '.first_name', 'has-error') : null }}">
            <input type="text" data-name="captains[*][first_name]" {!! isset($index) ? 'name="captains[' . $index . '][first_name]"' : '' !!} autocomplete="off" class="form-control data-first-name" value="{{ $first_name ?? '' }}" placeholder="First Name*">
            @if(isset($index))
                {!! $errors->first('captains.' . $index . '.first_name', '<span class="help-block">:message</span>') !!}
            @endif
        </div>
        <div class="col-2 {{ isset($index) ? $errors->first('captains.' . $index . '.last_name', 'has-error') : null }}">
            <input type="text" data-name="captains[*][last_name]" {!! isset($index) ? 'name="captains[' . $index . '][last_name]"' : '' !!} autocomplete="off" class="form-control data-last-name" value="{{ $last_name ?? '' }}" placeholder="Last Name*">
            @if(isset($index))
                {!! $errors->first('captains.' . $index . '.last_name', '<span class="help-block">:message</span>') !!}
            @endif
        </div>
        <div class="col-4 {{ isset($index) ? $errors->first('captains.' . $index . '.email', 'has-error') : null }}">
            <input type="text" data-name="captains[*][email]" {!! isset($index) ? 'name="captains[' . $index . '][email]"' : '' !!} autocomplete="off" class="form-control data-email" value="{{ $email ?? '' }}" placeholder="Email">
            @if(isset($index))
                {!! $errors->first('captains.' . $index . '.email', '<span class="help-block">:message</span>') !!}
            @endif
        </div>
        <div class="offset-2 col-2">
            <button class="btn data-row-delete" type="button">
                @lang('button.delete')
            </button>
        </div>
    </div>
    <div class="row mt-2">
        <div class="col-3 {{ isset($index) ? $errors->first('captains.' . $index . '.phone', 'has-error') : null }}">
            <input type="text" data-name="captains[*][phone]" {!! isset($index) ? 'name="captains[' . $index . '][phone]"' : '' !!} autocomplete="off" class="form-control data-phone" value="{{ $phone ?? '' }}" placeholder="Cell #">
            @if(isset($index))
                {!! $errors->first('captains.' . $index . '.phone', '<span class="help-block">:message</span>') !!}
            @endif
        </div>
        <div class="col-3 {{ isset($index) ? $errors->first('captains.' . $index . '.phone_home', 'has-error') : null }}">
            <input type="text" data-name="captains[*][phone_home]" {!! isset($index) ? 'name="captains[' . $index . '][phone_home]"' : '' !!} autocomplete="off" class="form-control data-phone-home" value="{{ $phone_home ?? '' }}" placeholder="Home #">
            @if(isset($index))
                {!! $errors->first('captains.' . $index . '.phone_home', '<span class="help-block">:message</span>') !!}
            @endif
        </div>
    </div>
</div>