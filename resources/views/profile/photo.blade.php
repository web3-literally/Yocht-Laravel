@extends('layouts.dashboard-profile')

@section('dashboard-content')
    <div class="container">
        <h3>@lang('general.account_photo')</h3>
        @parent
        <div class="row">
            <div class="col-12">
                @php
                    $parts = explode('.', Request::route()->getName());
                    $parts[] = 'update';
                @endphp
                {!! Form::model($user, ['url' => route(implode('.', $parts), Request::route()->parameters), 'method' => 'put', 'class' => 'form-horizontal', 'enctype'=>"multipart/form-data"]) !!}
                <div class="form-group row {{ $errors->first('pic', 'has-error') }}">
                    <label for="pic" class="col-sm-2 col-form-label">Profile Photo</label>
                    <div class="col-sm-10">
                        <div class="image">
                            <input type="file" name="pic" id="pic" class="form-control d-none">
                            <label class="btn btn--orange" for="pic">Click to Upload</label>
                        </div>
                        <span class="help-block">{{ $errors->first('pic', ':message') }}</span>
                        <div class="preview-photo">
                            <img class="rounded-avatar" src="{{ $user->getProfileThumb('100x100') }}" alt="">
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="offset-2 col-sm-10">
                        <button type="submit" class="btn btn-primary">@lang('button.save')</button>
                    </div>
                </div>
                {!!  Form::close()  !!}
            </div>
        </div>
    </div>
@stop

@section('footer_scripts')
    @parent
    <script>
        $(document).ready(function () {
            $('.image').each(function() {
                var self = $(this);
                $(this).find('input[type=file]').on('change', function(e) {
                    var input = $(this).get(0);
                    if (input.files.length) {
                        console.log($(this).data('selected'));
                    }
                    $('[for=' + $(this).attr('id') + ']', self).text('Selected');
                });
            });
        });
    </script>
@stop
