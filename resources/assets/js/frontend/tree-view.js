(function($) {
    $('body').on('click', '.tree-view .caret', function() {
        $(this).toggleClass('caret-down');
        $(this).next('.nested').toggleClass('active');
    });
})(jQuery);