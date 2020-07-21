@extends('layouts.dashboard-member')

@section('page_class')
    edit-vessel-about edit-vessel vessels @parent
@stop

@section('dashboard-content')
    <div class="dashboard-form-view">
        <h2>@lang('general.profile')</h2>
        @include('vessels._profile-nav')
        {{ Form::model($vessel, ['url' => route('account.vessels.profile.about.update', $vessel->id), 'id' => 'vessel-form', 'method' => 'post', 'files' => false]) }}
        <div class="container">
            <div class="row">
                <div class="col-md-12 content business-content mt-4 mb-4">
                    <div class="row">
                        <div class="col-md-12">
                            <h3>@lang('general.account_about')</h3>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group {{ $errors->first('description', 'has-error') }}">
                                {{ Form::textarea('description', null, ['class' => 'form-control', 'placeholder' => '', 'autocomplete' => 'off', 'id' => 'business_description']) }}
                                {!! $errors->first('description', '<span class="help-block">:message</span>') !!}
                            </div>
                        </div>
                    </div>
                    <hr>
                    {{ Form::submit(trans('button.save'), ['class' => 'btn btn--orange']) }}
                </div>
            </div>
        </div>
        {{ Form::close() }}
    </div>
@endsection

@section('footer_scripts')
    @parent
    <script type="text/javascript" src="{{ asset('assets/vendors/ckeditor/js/ckeditor.js') }}"></script>
    <script>
        CKEDITOR.replace('business_description', {
            removeButtons: 'Link,Unlink,Anchor,Image,Table,Format,Indent,Outdent',
            extraPlugins: 'autogrow',
            removePlugins: 'preview,sourcearea,resize',
            autoGrow_onStartup: true
        });
    </script>
@endsection