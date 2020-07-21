<div class="addthis_inline_share_toolbox" data-title="{{ $title }}" data-description="{{ $description }}"></div>

@section('footer_scripts')
    @parent
    <script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid={{ config('social_share.id') }}"></script>
@stop