@extends('layouts.dashboard-member')

@section('page_class')
    ticket-details dashboard-tickets @parent
@stop

@section('dashboard-content')
    @parent
    <div class="container">
        <h2>
            <span>#{{ $ticket->id }}</span> <span>{{ $ticket->job->title }}</span>
        </h2>
        @parent
        <div class="row">
            <div class="col-lg-12 col-12">
                <div class="white-content-block p-5">
                    @if($ticket->job->starts_at)
                        <div class="start-date float-right">
                            <b>Employment Start Date</b>
                            <i class="far fa-clock pl-2 pr-2"></i>
                            <b>{{ $ticket->job->starts_at->toFormattedDateString() }}</b>
                        </div>
                    @endif
                    @if($ticket->job->vessel_id)
                        <h3><a href="{{ $ticket->job->vessel->user->getPublicProfileLink() }}" class="link link--orange">{{ $ticket->job->vessel->name }}</a></h3>
                    @endif
                    @if ($address = $ticket->job->location_address)
                        <div class="address pb-3"><i class="color-orange fas fa-map-marker-alt"> </i> {{ $address }}</div>
                    @endif
                    {!! $ticket->job->content !!}
                    @php(list($lat, $lng) = array_values($ticket->job->location_map))
                    @if ($lat && $lng)
                        @widget('Map', ['id' => 'job-map', 'class' => 'side-bar-section pt-3', 'lat' => $lat, 'lng' => $lng, 'height' =>'330px', 'zoom' => 11])
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection