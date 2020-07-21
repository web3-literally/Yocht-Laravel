<div style="position: absolute">
    <div class="spacer s0" id="trigger"></div>
    <div id="target">
        {{--<img id="plane" src="../../img/example_bezier.png" style="background: red">--}}
        <div id="plane" class="animating-yacht-block"></div>
    </div>
</div>


@section('footer_scripts')
    @parent
    <script type="text/javascript"  src="{{ asset('assets/js/frontend/TweenMax.min.js') }}"></script>
    <script type="text/javascript"  src="{{ asset('assets/js/frontend/ScrollMagic.min.js') }}"></script>
    <script type="text/javascript"  src="{{ asset('assets/js/frontend/animation.gsap.js') }}"></script>
    <script type="text/javascript"  src="{{ asset('assets/js/frontend/animatingYacht.js') }}"></script>
@stop