@section('google_map_script')
    <script src="//maps.google.com/maps/api/js?key={{ config('services.google_map.key') }}&libraries=places&sensor=false"></script>
@stop