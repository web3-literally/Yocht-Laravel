<div class="container-fluid data-row mb-3" {!! isset($index) ? 'data-index="' . $index . '"' : '' !!}>
    <div class="row">
        <div class="col-3 {{ isset($index) ? $errors->first('managers.' . $index . '.email', 'has-error') : null }}">
            <input type="text" data-name="managers[*][email]" {!! isset($index) ? 'name="managers[' . $index . '][email]"' : '' !!} autocomplete="off" class="form-control data-email" value="{{ $email ?? '' }}" placeholder="Email*">
            @if(isset($index))
                {!! $errors->first('managers.' . $index . '.email', '<span class="help-block">:message</span>') !!}
            @endif
        </div>
        <div class="col-2 {{ isset($index) ? $errors->first('managers.' . $index . '.first_name', 'has-error') : null }}">
            <input type="text" data-name="managers[*][first_name]" {!! isset($index) ? 'name="managers[' . $index . '][first_name]"' : '' !!} autocomplete="off" class="form-control data-first-name" value="{{ $first_name ?? '' }}" placeholder="First Name*">
            @if(isset($index))
                {!! $errors->first('managers.' . $index . '.first_name', '<span class="help-block">:message</span>') !!}
            @endif
        </div>
        <div class="col-2 {{ isset($index) ? $errors->first('managers.' . $index . '.last_name', 'has-error') : null }}">
            <input type="text" data-name="managers[*][last_name]" {!! isset($index) ? 'name="managers[' . $index . '][last_name]"' : '' !!} autocomplete="off" class="form-control data-last-name" value="{{ $last_name ?? '' }}" placeholder="Last Name*">
            @if(isset($index))
                {!! $errors->first('managers.' . $index . '.last_name', '<span class="help-block">:message</span>') !!}
            @endif
        </div>
        <div class="col-3 {{ isset($index) ? $errors->first('managers.' . $index . '.phone', 'has-error') : null }}">
            <input type="text" data-name="managers[*][phone]" {!! isset($index) ? 'name="managers[' . $index . '][phone]"' : '' !!} autocomplete="off" class="form-control data-phone" value="{{ $phone ?? '' }}" placeholder="Phone*">
            @if(isset($index))
                {!! $errors->first('managers.' . $index . '.phone', '<span class="help-block">:message</span>') !!}
            @endif
        </div>
        <div class="col-2">
            <button class="btn data-row-delete" type="button">
                @lang('button.delete')
            </button>
        </div>
    </div>
</div>