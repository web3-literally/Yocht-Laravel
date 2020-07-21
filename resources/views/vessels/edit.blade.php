@extends('layouts.dashboard-member')

@section('page_class')
    edit-vessel-details edit-vessel vessels @parent
@stop

@section('dashboard-content')
    <div class="dashboard-form-view">
        <a class="btn btn--orange float-right" href="{{ route('account.tenders.add', ['parent_id' => $vessel->id]) }}" role="button">@lang('vessels.add_tender')</a>
        <h2>@lang('vessels.vessel_profile')</h2>
        @include('vessels._profile-nav')
        {{ Form::model($vessel, ['url' => route('account.vessels.update', $vessel->id), 'id' => 'vessel-form', 'method' => 'post', 'files' => true]) }}
        <div class="container">
            <div class="row">
                <div class="col-md-12 content vessel-content mt-4 mb-4">
                    @include('vessels.fields')
                    <hr>
                    {{ Form::submit(trans('button.save'), ['class' => 'btn btn--orange']) }}
                </div>
            </div>
        </div>
        {{ Form::close() }}
    </div>
@endsection