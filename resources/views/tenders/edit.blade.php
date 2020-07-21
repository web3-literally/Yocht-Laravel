@extends('layouts.dashboard-member')

@section('page_class')
    edit-vessel vessels @parent
@stop

@section('dashboard-content')
    <div class="dashboard-form-view">
        <h2>@lang('vessels.tender_profile')</h2>
        {{ Form::model($vessel, ['url' => route('account.tenders.update', $vessel->id), 'id' => 'vessel-form', 'method' => 'post', 'files' => true]) }}
        <div class="container">
            <div class="row">
                <div class="col-md-12 content vessel-content mt-4 mb-4">
                    @include('tenders.fields')
                    <hr>
                    {{ Form::submit(trans('button.save'), ['class' => 'btn btn--orange']) }}
                </div>
            </div>
        </div>
        {{ Form::close() }}
    </div>
@endsection

@section('dashboard-top-vessel-location')
   {{-- Nothing to show --}}
@stop