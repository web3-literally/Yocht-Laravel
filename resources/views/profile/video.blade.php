@extends('layouts.dashboard-profile')

@section('header_styles')
    @parent
    <link href="{{ asset('assets/css/frontend/jquery.fileupload.css') }}" rel="stylesheet" />
@stop

@section('dashboard-content')
    <div class="container">
        <h3>@lang('general.account_video')</h3>
        @parent
        <div class="row">
            <div class="col-12">
                <div class="form text-center">
                    @php($link = $user->profile->attachments()->where('type', 'video')->first())
                    @if ($link)
                        <div class="video-container text-center">
                            <video class="vid w-75" controls>
                                <source src="{{ $link->file->getFileUrl() }}" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        </div>
                        <div>
                            <a href="{{ relative_route('video.delete') }}" class="btn btn--orange mt-3" onclick="return confirm('Are you sure you want to delete video?');">@lang('button.delete')</a>
                        </div>
                    @else
                        <span class="btn btn--orange fileinput-button">
                            <span>Upload</span>
                            <input id="fileupload" type="file" name="file" disabled="disabled" accept="video/mp4">
                        </span>
                        <div id="progress" class="progress d-none mt-3">
                            <div class="progress-bar progress-bar-success"></div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop

@section('footer_scripts')
    @parent
    <script src="{{ asset('assets/js/frontend/jquery.iframe-transport.js') }}" ></script>
    <script src="{{ asset('assets/js/frontend/jquery.fileupload.js') }}" ></script>
    <script src="{{ asset('assets/js/frontend/jquery.fileupload-process.js') }}" ></script>
    <script src="{{ asset('assets/js/frontend/jquery.fileupload-validate.js') }}" ></script>
    <script>
        $(function () {
            $('#fileupload').fileupload({
                url: "{{ relative_route('video.update') }}",
                dataType: 'json',
                autoUpload: true,
                acceptFileTypes: /(\.|\/)(mp4)$/i,
                maxFileSize: 100000000, // 100 MB
                maxNumberOfFiles: 1,
                start: function (e) {
                    $('#progress').removeClass('d-none');
                },
                stop: function (e) {
                    $('#progress').addClass('d-none');
                },
                fail: function (e, data) {
                    var message = data.jqXHR.responseJSON.message;
                    if (!message) {
                        message = 'An unknown error has occurred';
                    }
                    bootbox.alert(message);
                },
                done: function (e, data) {
                    var file = data.result.file;
                    if (file.error) {
                        bootbox.alert(file.error);
                    } else {
                        window.location.href = "{{ route(Request::route()->getName()) }}";
                    }
                },
                progressall: function (e, data) {
                    var progress = parseInt(data.loaded / data.total * 100, 10);
                    $('#progress .progress-bar').css(
                        'width',
                        progress + '%'
                    );
                },
            }).prop('disabled', !$.support.fileInput)
                .parent().addClass($.support.fileInput ? undefined : 'disabled');
        });
    </script>
@stop