$.fn.select2flags = function (options) {
    var defaults = {
        placeholder: this.data('placeholder'),
        theme: 'bootstrap',
        allowClear: true
    };
    var settings = $.extend({}, defaults, options);

    var select2FormatFlag = function (state) {
        if (!state.id) {
            return $('<span>' + (state.text ? state.text : '&nbsp;') + '</span>');
        }
        return $('<span><span class="flag-icon flag-icon-' + state.element.value.toLowerCase() + '"></span> ' + state.text + '</span>');
    };
    settings.templateResult = select2FormatFlag;
    settings.templateSelection = select2FormatFlag;

    this.select2(settings);

    return this;
};
