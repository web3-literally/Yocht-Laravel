<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            @include('dashboard._messages')
        </div>
    </div>
</div>
<div class="container-fluid">
    @if(Sentinel::check() && Sentinel::getUser()->hasAccess('jobs.manage'))
        <div class="row mb-4">
            {{--  dashboard-search  --}}
            <div class="col-12 d-flex dashboard-search">
                <div class="h-100 w-100 dashboard-grid-item">
                    @widget('JobsFindMembers')
                </div>
            </div>
        </div>
    @endif
    <div class="row mb-4">
        {{--  dashboard-reminders  --}}
        <div class="col-md-7 col-sm-12 d-flex align-items-stretch dashboard-reminders">
            <div class="h-100 w-100 dashboard-grid-item">
                @widget('VesselReminders')
            </div>
        </div>
        {{--  dashboard-map  --}}
        <div class="col-md-5 col-sm-12 d-flex align-items-stretch dashboard-map">
            <div class="h-100 w-100 dashboard-grid-item">
                @widget('Travels')
            </div>
        </div>
    </div>
    <div class="row mb-4">
        {{--  dashboard-weather  --}}
        <div class="col-3 d-flex align-items-stretch dashboard-weather">
            <div class="h-100 w-100 dashboard-grid-item">
                <div class="h-100 w-100 dashboard-grid-item">
                    @widget('Weather')
                </div>
            </div>
        </div>
        {{--  dashboard-today-tides  --}}
        <div class="col-4 d-flex align-items-stretch dashboard-day-tides">
            @widget('DayTides')
        </div>
        <div class="col-5 px-0 d-flex align-items-stretch">
            <div class="h-100 w-100" class="row">
                {{--  dashboard-rise  --}}
                <div class="col-12 mb-4 d-flex align-items-stretch dashboard-sunmoon-time">
                    @widget('SunMoonTime')
                </div>
                {{--  dashboard-tide  --}}
                <div class="col-12 d-flex align-items-stretch dashboard-tide">
                    <div class="h-100 w-100 dashboard-grid-item">
                        @widget('TideChart')
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-md-12 col-sm-12 px-0 d-flex align-items-stretch">
            <div class="h-100 w-100" class="row">
                {{--  dashboard-doc-storage  --}}
                @if(Sentinel::getUser()->hasAccess('vessels.documents'))
                    <div class="col-12 mb-0 d-flex align-items-stretch dashboard-doc-storage">
                        <div class="h-100 w-100 dashboard-grid-item">
                            @widget('VesselDocumentsStorage')
                        </div>
                    </div>
                @endif
                {{--  dashboard-guest  --}}
                {{--<div class="col-12 mb-4 d-flex align-items-stretch dashboard-guest">
                    <div class="h-100 w-100 dashboard-grid-item">
                        @widget('Guests')
                    </div>
                </div>--}}
            </div>
        </div>
    </div>
    {{--<div class="row mb-4">
        --}}{{--  dashboard-resources  --}}{{--
        <div class="col-12 d-flex dashboard-resources">
            <div class="h-100 w-100 dashboard-grid-item">
                @widget('Resources')
            </div>
        </div>
    </div>--}}
</div>
