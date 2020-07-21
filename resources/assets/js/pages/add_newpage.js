$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('#page-grid').html($('#page-content').val());

    $('#page-grid').gridEditor({
        row_classes: [],
        col_classes: [],
        new_row_layouts: [[12], [6, 6], [9, 3], [3, 9], [4, 4, 4], [3, 3, 3, 3]],
        content_types: ['ckeditor'],
        ckeditor: {
            config: {
                extraAllowedContent: '*[id](*); div; span',
                extraPlugins: 'sourcedialog,font,image2,uploadimage',
                removePlugins: 'preview,sourcearea',
                removeDialogTabs: 'image:advanced;link:advanced',
                filebrowserImageUploadUrl: '/admin/pages/upload'
            }
        },
    });

    $('#page-content-form').on('submit', function () {
        var html = $('#page-grid').gridEditor('getHtml');
        $('#page-content').val(String(html).trim());
    });
});