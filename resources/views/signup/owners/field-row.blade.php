<div class="container-fluid data-row mb-3" {!! isset($index) ? 'data-index="' . $index . '"' : '' !!}>
    <div class="row">
        <div class="col-3 {{ isset($index) ? $errors->first('owners.' . $index . '.email', 'has-error') : null }}">
            <input type="text" data-name="owners[*][email]" {!! isset($index) ? 'name="owners[' . $index . '][email]"' : '' !!} autocomplete="off" class="form-control data-email" value="{{ $email ?? '' }}" placeholder="Email*">
            @if(isset($index))
                {!! $errors->first('owners.' . $index . '.email', '<span class="help-block">:message</span>') !!}
            @endif
        </div>
        <div class="col-2 {{ isset($index) ? $errors->first('owners.' . $index . '.first_name', 'has-error') : null }}">
            <input type="text" data-name="owners[*][first_name]" {!! isset($index) ? 'name="owners[' . $index . '][first_name]"' : '' !!} autocomplete="off" class="form-control data-first-name" value="{{ $first_name ?? '' }}" placeholder="First Name*">
            @if(isset($index))
                {!! $errors->first('owners.' . $index . '.first_name', '<span class="help-block">:message</span>') !!}
            @endif
        </div>
        <div class="col-2 {{ isset($index) ? $errors->first('owners.' . $index . '.last_name', 'has-error') : null }}">
            <input type="text" data-name="owners[*][last_name]" {!! isset($index) ? 'name="owners[' . $index . '][last_name]"' : '' !!} autocomplete="off" class="form-control data-last-name" value="{{ $last_name ?? '' }}" placeholder="Last Name*">
            @if(isset($index))
                {!! $errors->first('owners.' . $index . '.last_name', '<span class="help-block">:message</span>') !!}
            @endif
        </div>
        <div class="col-3 {{ isset($index) ? $errors->first('owners.' . $index . '.phone', 'has-error') : null }}">
            <input type="text" data-name="owners[*][phone]" {!! isset($index) ? 'name="owners[' . $index . '][phone]"' : '' !!} autocomplete="off" class="form-control data-phone" value="{{ $phone ?? '' }}" placeholder="Phone">
            @if(isset($index))
                {!! $errors->first('owners.' . $index . '.phone', '<span class="help-block">:message</span>') !!}
            @endif
        </div>
        <div class="col-2">
            <button class="btn data-row-delete" type="button">
                @lang('button.delete')
            </button>
        </div>
    </div>
</div>