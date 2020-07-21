<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            @include('dashboard._messages')
        </div>
    </div>
</div>
<div class="container-fluid">
    <div class="row mb-4">
        {{--  dashboard-map  --}}
        <div class="offset-7 col-md-5 col-sm-12 d-flex align-items-stretch dashboard-map">
            <div class="h-100 w-100 dashboard-grid-item">
                @widget('Travels')
            </div>
        </div>
    </div>
    <div class="row mb-4">
        {{--  dashboard-weather  --}}
        <div class="col-md-4 col-sm-12 d-flex align-items-stretch dashboard-weather">
            <div class="h-100 w-100 dashboard-grid-item">
                @widget('Weather')
            </div>
        </div>
        <div class="col-md-8 col-sm-12 px-0 d-flex align-items-stretch ">
            <div class="h-100 w-100" class="row">
                {{--  dashboard-tide  --}}
                <div class="col-12 mb-4 d-flex align-items-stretch dashboard-tide">
                    <div class="h-100 w-100 dashboard-grid-item">
                        @widget('TideTable')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>