@php($view = $_COOKIE['view_layout'] ?? 'list')
<div class="view-switcher">
    <ul class="list-unstyled">
        <li class="{{ $view != 'grid' ? 'active' : '' }}"><a href="#list"><i class="fas fa-list"></i></a></li>
        <li class="{{ $view == 'grid' ? 'active' : '' }}"><a href="#grid"><i class="fas fa-grip-vertical"></i></a></li>
    </ul>
</div>

@section('footer_scripts')
    @parent
    <script>
        $(function() {
            var el = $('.view-switch-class').first();
            if (el.length) {
                $('.view-switcher a').on('click', function() {
                    if (!$(this).parent().hasClass('active')) {
                        $(this).closest('ul').children('li').toggleClass('active');
                        el.toggleClass('view-list').toggleClass('view-grid');
                        setCookie('view_layout', el.hasClass('view-list') ? 'list' : 'grid');
                    }
                    return false;
                });
            }
        });
    </script>
@stop