@extends('layouts.dashboard-member')

@section('page_class')
    edit-vessel-video edit-vessel vessels @parent
@stop

@section('header_styles')
    @parent
    <link href="{{ asset('assets/css/frontend/jquery.fileupload.css') }}" rel="stylesheet" />
@stop

@section('dashboard-content')
    <div class="dashboard-form-view">
        @parent
        <h2>@lang('general.profile')</h2>
        @include('vessels._profile-nav')
        <div class="container white-content-block">
            <div class="row">
                <div class="col-md-12 content vessel-content mt-4 mb-4 text-center">
                    @php($link = $vessel->attachments()->where('type', 'video')->first())
                    @if ($link)
                        <div class="video-container text-center">
                            <video class="vid w-75" controls>
                                <source src="{{ $link->file->getFileUrl() }}" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        </div>
                        <div>
                            <a href="{{ route('account.vessels.profile.video.delete', ['boat_id' => $vessel->id]) }}" class="btn btn--orange mt-3" onclick="return confirm('Are you sure you want to delete video?');">@lang('button.delete')</a>
                        </div>
                    @else
                        <span class="btn btn--orange fileinput-button">
                            <span>@lang('general.upload')</span>
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
@endsection

@section('footer_scripts')
    @parent
    <script src="{{ asset('assets/js/frontend/jquery.iframe-transport.js') }}" ></script>
    <script src="{{ asset('assets/js/frontend/jquery.fileupload.js') }}" ></script>
    <script src="{{ asset('assets/js/frontend/jquery.fileupload-process.js') }}" ></script>
    <script src="{{ asset('assets/js/frontend/jquery.fileupload-validate.js') }}" ></script>
    <script>
        $(function () {
            $('#fileupload').fileupload({
                url: "{{ route('account.vessels.profile.video.store', ['boat_id' => $vessel->id]) }}",
                dataType: 'json',
                autoUpload: true,
                acceptFileTypes: /(\.|\/)(mp4)$/i,
                maxFileSize: 40000000, // 40 MB
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
                        window.location.href = "{{ route('account.vessels.profile.video', ['boat_id' => $vessel->id]) }}";
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