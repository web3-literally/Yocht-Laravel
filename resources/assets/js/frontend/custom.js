$(document).ready(function () {
    $(document).on('click', '.card-heading .removepanel', function () {
        var $this = $(this);
        $this.parents('.card').hide("slow");
    });
    //panel hide
    $('.showhide').attr('title', 'Hide Panel content');
    $(document).on('click', '.card-heading .clickable', function (e) {
        var $this = $(this);
        if (!$this.hasClass('panel-collapsed')) {
            $this.parents('.card').find('.card-body').slideUp();
            $this.addClass('panel-collapsed');
            $this.find('i').removeClass('fa-chevron-up').addClass('fa-chevron-down');
            $('.showhide').attr('title', 'Show Panel content');
        } else {
            $this.parents('.card').find('.card-body').slideDown();
            $this.removeClass('panel-collapsed');
            $this.find('i').removeClass('fa-chevron-down').addClass('fa-chevron-up');
            $('.showhide').attr('title', 'Hide Panel content');
        }
    });
});

