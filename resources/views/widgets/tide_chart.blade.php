<div id="tide-table-widget">
    <h4>Tide Table<div class="location"><i class="fa fa-map-marker-alt"></i><strong>Your Location:</strong> {{ $location }}</div></h4>
    {!! $chart->html() !!}
</div>

@section('footer_scripts')
    @parent
    <script src="{{ asset('assets/js/raphael.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/morrisjs/morris.min.js') }}"></script>
    {!! Charts::scripts() !!}
    {!! $chart->script() !!}
@stop
