<div class="site-statistic-widget">
    <div class="d-flex justify-content-between">
        <div class="statistic">
            <span class="value">
                <span class="counter">100</span>
                <span class="metric percentage">%</span>
            </span>
            <span class="label">Satisfaction</span>
        </div>
        <div class="statistic">
            <span class="value">
                <span class="counter">250</span>
                <span class="metric">+</span>
            </span>
            <span class="label">Experts</span>
        </div>
        <div class="statistic">
            <span class="value">
                <span class="counter">130</span>
                <span class="metric">+</span>
            </span>
            <span class="label">Vessels</span>
        </div>
        <div class="statistic">
            <span class="value">
                <span class="counter">743</span>
                <span class="metric">+</span>
            </span>
            <span class="label">Boats</span>
        </div>
    </div>
</div>

@section('footer_scripts')
    @parent
    <script type="text/javascript" src="{{ asset('assets/js/frontend/jquery.counterup.js') }}"></script>
    <script>
        $('.site-statistic-widget .counter').counterUp({
            delay: 10,
            time: 1000
        });
    </script>
@stop