<div class="share">
    <div class="addthis_toolbox">
        <a class="addthis_button_more"><i class="fas fa-plus"></i></a>
        <a class="addthis_button_email"><i class="fas fa-envelope"></i></a>
        <a class="addthis_button_twitter"><i class="fab fa-twitter"></i></a>
        <a class="addthis_button_facebook"><i class="fab fa-facebook-f"></i></a>
    </div>
</div>

@section('footer_scripts')
    @parent
    <script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid={{ config('social_share.id') }}"></script>
@stop