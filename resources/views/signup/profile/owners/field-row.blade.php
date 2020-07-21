<div class="container-fluid data-row mb-3" {!! isset($index) ? 'data-index="' . $index . '"' : '' !!}>
    <div class="row">
        <div class="col-2 {{ isset($index) ? $errors->first('profile.owners.' . $index . '.first_name', 'has-error') : null }}">
            <input type="text" data-name="profile[owners][*][first_name]" {!! isset($index) ? 'name="profile[owners][' . $index . '][first_name]"' : '' !!} autocomplete="off" class="form-control data-first-name" value="{{ $first_name ?? '' }}" placeholder="First Name*">
            @if(isset($index))
                {!! $errors->first('profile.owners.' . $index . '.first_name', '<span class="help-block">:message</span>') !!}
            @endif
        </div>
        <div class="col-2 {{ isset($index) ? $errors->first('profile.owners.' . $index . '.last_name', 'has-error') : null }}">
            <input type="text" data-name="profile[owners][*][last_name]" {!! isset($index) ? 'name="profile[owners][' . $index . '][last_name]"' : '' !!} autocomplete="off" class="form-control data-last-name" value="{{ $last_name ?? '' }}" placeholder="Last Name*">
            @if(isset($index))
                {!! $errors->first('profile.owners.' . $index . '.last_name', '<span class="help-block">:message</span>') !!}
            @endif
        </div>
        <div class="col-3 {{ isset($index) ? $errors->first('profile.owners.' . $index . '.email', 'has-error') : null }}">
            <input type="text" data-name="profile[owners][*][email]" {!! isset($index) ? 'name="profile[owners][' . $index . '][email]"' : '' !!} autocomplete="off" class="form-control data-email" value="{{ $email ?? '' }}" placeholder="Email">
            @if(isset($index))
                {!! $errors->first('profile.owners.' . $index . '.email', '<span class="help-block">:message</span>') !!}
            @endif
        </div>
        <div class="col-3 {{ isset($index) ? $errors->first('profile.owners.' . $index . '.phone', 'has-error') : null }}">
            <input type="text" data-name="profile[owners][*][phone]" {!! isset($index) ? 'name="profile[owners][' . $index . '][phone]"' : '' !!} autocomplete="off" class="form-control data-phone" value="{{ $phone ?? '' }}" placeholder="Phone">
            @if(isset($index))
                {!! $errors->first('profile.owners.' . $index . '.phone', '<span class="help-block">:message</span>') !!}
            @endif
        </div>
        <div class="col-2">
            <button class="btn data-row-delete" type="button">
                @lang('button.delete')
            </button>
        </div>
    </div>
</div>