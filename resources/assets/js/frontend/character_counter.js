$(function () {
    // Character counter
    $('textarea.with-counter').on('keyup', function () {
        var id = $(this).data('counter-id');
        var max = $(this).attr('maxlength');
        var str = $(this).val().length;
        if (max) {
            str += ' / ' + Number(max);
        }
        $('#' + id).text(str);
    }).keyup();
});