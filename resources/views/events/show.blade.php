@extends('layouts.component.component-side-right')

@section('page_class')
    event-details events @parent
@stop

@section('right-content')
    <div class="side-bar component-box">
        @include('events._search_form')
        @widget('FollowUs')
        @widget('Map', ['id' => 'event-map', 'class' => 'side-bar-section', 'address' => $event->address, 'height' => '330px', 'zoom' => 11])
        @widget('EventsCalendar')
        @widget('UpcomingEvents')
        @widget('EventsCategories')
    </div>
@endsection

@section('center-content')
    <div class="container">
        <div class="main-content">
            <div class="component-box">

                <div class="event-details-banner" style="background-image: url('{{ $event->getThumb('1100x680') }}')">
                    <div class="gradient">
                        <div class="info-block">
                            <div class="banner-share">
                                <i class="fas fa-link"></i>
                                <span>Like This, Share it:</span>
                                @widget('ShareIcons')
                            </div>
                            <div>
                                <span class="category">Fabrication</span>
                            </div>
                            <div>
                                <i class="far fa-calendar-alt"></i>
                                <small>Starts at: {{ $event->starts_at->format('M j, Y, g:i a') }}</small>
                                <br>
                                <i class="far fa-calendar-alt"></i>
                                <small>Ends at: {{ $event->ends_at->format('M j, Y, g:i a') }}</small>
                            </div>
                            <div>
                                <i class="fas fa-map-marker-alt"></i>
                                <small>{{ $event->address }}</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="container under-banner">
                    <div>
                        Posted by <a href="{{ route('members.show', ['id' => $event->user->id]) }}">{{ $event->user->member_title }}</a>
                        on {{ $event->updated_at->toFormattedDateString() }}
                        in <span>{{ $event->category->label }}</span>
                    </div>
                    <div>
                        <strong>Price:</strong>
                        @if(empty($event->price))
                            <span>Free</span>
                        @else
                            <span>{{ Shop::format((int)$event->price) }}</span>
                        @endif
                    </div>
                </div>

                <div class="container content">
                    <h1>{{ $event->title }}</h1>
                    <hr>
                    {!! $event->description !!}
                </div>
            </div>
        </div>
    </div>
@endsection
