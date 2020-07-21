$(function () {
    CKEDITOR.replace('event-description', {
        removeButtons: 'Link,Unlink,Anchor,Image,Table,Format,Indent,Outdent,HorizontalRule',
        removePlugins: 'preview,sourcearea,resize'
    });
    $("#event-starts-at-alt").datepicker({
        altField: "#event-starts-at",
        altFormat: "yy-mm-dd",
        minDate: '+1d',
        onSelect: function (dateText) {
            var min = new Date(dateText);
            $("#event-ends-at-alt").datepicker('option', 'minDate', min);
        }
    });
    $("#event-ends-at-alt").datepicker({
        altField: "#event-ends-at",
        altFormat: "yy-mm-dd",
        minDate: '+1d'
    });
});