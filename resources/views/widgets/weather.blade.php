<div id="weather-widget" class="weather-widget">
    {{ Form::open(['route' => 'dashboard.weather', 'onsubmit' => 'return false;', 'id' => $config['id'] ?? 'search-location-form', 'class' => 'search-form', 'method' => 'GET']) }}
    <div class="input-group mb-3 search-field {{ $errors->first('search', 'has-error') }}">
        {{ Form::text('q', request('q', null), ['class' => 'form-control p-dark', 'autocomplete' => 'off', 'placeholder' => trans('general.search_location_placeholder')]) }}
        <div class="input-group-append">
            <button class="btn btn-outline-secondary icon-search" type="submit">
                <i class="fa fa-search"></i>
            </button>
        </div>
        {!! $errors->first('q', '<span class="help-block">:message</span>') !!}
    </div>
    {{ Form::close() }}
    <div class="info-panel d-flex justify-content-around">
        <div class="weather-temperature" title="Temperature">
            <div class="d-flex justify-content-start">
                <div class="val">{{ $temperature }}</div>
                <div class="{{ $temperatureUnitClass }} align-self-center"></div>
            </div>
        </div>
        <div class="weather-wind-speed {{ $weatherWindSpeed > 9 ? 'w2' : 'w1' }}" title="Wind">
            <div class="d-flex justify-content-start">
                <div class="val">{{ $weatherWindSpeed }}</div>
                <div class="extra align-self-center">
                    <div class="unit">{{ $weatherWindSpeedUnit }}</div>
                    <div class="direction">{{ $weatherWindDirection }}</div>
                </div>
            </div>
        </div>
        @if($weatherIcon)
            <div class="weather-status align-self-center" title="{{ $weatherIconPhrase }}" style="background: url('{{ $weatherIcon }}') no-repeat center center"></div>
        @endif
    </div>
    <h4>{{ $location }}</h4>
    @if ($weatherDetails)
        <a class="details" target="_blank" href="{{ $weatherDetails }}" title="Details"><i class="fas fa-info-circle"></i></a>
    @endif
</div>

@section('footer_scripts')
    @parent
    <script>
        $(function () {
            $('body').on('current-location.changed', function () {
                $('#weather-widget').parent().loading();
                $('#weather-widget').parent().load("{{ route('dashboard.weather') }} #weather-widget", function (response, status, xhr) {
                    $('#weather-widget').parent().loading('stop');
                });
            });
            $('#weather-widget').parent().on('submit', 'form', function () {
                $('#weather-widget').parent().loading();
                $('#weather-widget').parent().load($(this).attr('action') + " #weather-widget", $(this).serialize(), function (response, status, xhr) {
                    $('#weather-widget').parent().loading('stop');
                });
                return false;
            });
        });
    </script>
@endsection
