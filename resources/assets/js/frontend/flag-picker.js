//@depreceted
var select2FormatFlag = function (state) {
    if (!state.id) {
        return state.text;
    }
    var el = $(
        '<span><span class="flag-icon flag-icon-'+state.element.value.toLowerCase()+'"></span> ' + state.text + '</span>'
    );
    return el;
};

$(function () {
    $('select.flag-picker').each(function(i, el) {
        var placeholder = $(el).data('placeholder');
        var select2FormatFlagParams = {
            templateResult: select2FormatFlag,
            templateSelection: select2FormatFlag,
            placeholder: placeholder ? placeholder : 'select a flag',
            theme: 'bootstrap',
            allowClear: true
        };
        $(el).select2(select2FormatFlagParams)
    });
});
