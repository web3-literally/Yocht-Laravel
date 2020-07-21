$('#dashboard-notification .notification-toggle').on('click', function (e) {
    e.stopPropagation();
    $(this).parent().find('.notification-dropdown').show();
    return false;
});
$('body').on('click', function () {
    $('#dashboard-notification .notification-dropdown').hide();
});

$('#dashboard-notification .messages-toggle').on('click', function (e) {
    e.stopPropagation();
    $(this).parent().find('.messages-dropdown').show();
    return false;
});
$('body').on('click', function () {
    $('#dashboard-notification .messages-dropdown').hide();
});