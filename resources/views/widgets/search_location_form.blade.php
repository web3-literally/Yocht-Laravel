<div id="search-by-location-widget" class="search-form-widget">
    {{ Form::open(['route' => 'members.search', 'id' => $config['id'] ?? 'search-location-form', 'class' => 'search-form', 'method' => 'GET']) }}
        <div class="input-group mb-3 search-field {{ $errors->first('location', 'has-error') }}">
            {{ Form::text('location', request('location', request()->cookie('current_location')), ['class' => 'form-control p-dark', 'autocomplete' => 'off', 'placeholder' => trans('general.search_location_placeholder')]) }}
            <div class="input-group-append">
                <button class="btn btn-outline-secondary icon-search" type="submit">
                    <i class="fa fa-search"></i>
                </button>
            </div>
            {!! $errors->first('location', '<span class="help-block">:message</span>') !!}
        </div>
        {{ Form::hidden('group', 'businesses') }}
    {{ Form::close() }}
    <div class="results d-none">
        <ul class="results-list"></ul>
    </div>
</div>

@include('google-map')
@section('footer_scripts')
    @parent
    <script src="{{ asset('assets/js/frontend/search-by-location.js') }}" type="text/javascript"></script>
    <script>
        $(function () {
            $('body').on('current-location.detecting', function (event) {
                $('#search-location-form').find('input[name=location]').loading();
            });
            $('body').on('current-location.completed', function (event) {
                $('#search-location-form').find('input[name=location]').loading('stop');
            });
            $('body').on('current-location.changed', function (event, location) {
                $('#search-location-form').find('input[name=location]').val(location);
            });
        });
        $(window).load(function() {
            $("#{{ $config['id'] ?? 'search-location-form' }}-submit").on('click', function() {
                $("#{{ $config['id'] ?? 'search-location-form' }}").submit();
            });
        });
    </script>
@stop
