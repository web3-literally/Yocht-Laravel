$(function () {
    $('#job-vessel').on('change', function () {
        if ($(this).val() === '') {
            $('.job-without-vessel').removeClass('d-none');
        } else {
            $('.job-without-vessel').addClass('d-none');
        }
    }).change();

    // Starts At

    $("#job-starts-at-alt").datepicker({
        altField: "#job-starts-at",
        altFormat: "yy-mm-dd",
        minDate: '+1d'
    });
});