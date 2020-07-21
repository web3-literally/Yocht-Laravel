$(document).ready(function() {
    $('.search-form').each(function(i, el) {
        var form = $(el)
        form.find('[type=submit]').on('click', function() {
            var q = String(form.find('input[name=q]').val());
            if (!q.trim()) {
                form.find('input[name=q]').focus();
                return false;
            }
        });
    });
});
