$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    CKEDITOR.replace('editor', {
        extraPlugins: 'sourcedialog,image2,uploadimage,autogrow,wpmore',
        removePlugins: 'preview,sourcearea,resize',
        removeDialogTabs: 'image:advanced;link:advanced',
        filebrowserImageUploadUrl: '/admin/blog/upload',
        autoGrow_onStartup: true
    });

    $('#blog_category').select2();

    $('#blog_publish_on').datetimepicker({
        defaultDate: new Date(),
        time: true,
        todayHighlight: 1,
        startView: 2,
        forceParse: 0
    });

    $('.form-group input[name=image]').attr("accept","image/*");

    $('.modal-footer').addClass('mx-auto');
});