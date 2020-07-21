@extends('layouts.dashboard-member')

@section('page_class')
    messenger-messages dashboard-tickets @parent
@stop

@section('header_styles')
    @parent
    <link href="{{ asset('assets/css/frontend/jquery.fileupload.css') }}" rel="stylesheet" />
@stop

@section('dashboard-content')
    <div class="dashboard-list-view">
        <h2>@lang('message.messages_with', ['name' => $thread->directUser()->full_name])</h2>
        <div class="container pl-0 pr-0">
            <div class="row">
                <div class="col-md-9">
                    <div class="thread">
                        <div class="dashboard-form-view">
                            @include('tickets.partials.form-message')
                        </div>
                        <div class="dashboard-list messages">
                            @each('messenger.partials.messages', $messages, 'message')
                        </div>
                        {{ $messages->links() }}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="attachments">
                        <div class="alert alert-info">Attach estimates, invoices and other important documents to this job listing.</div>
                        <h4>Documents</h4>
                        <ul id="files" class="attachments-list">
                            @foreach($ticket->application->attachments as $link)
                                <li>
                                    <a href="{{ route('account.tickets.attachments.download', ['id' => $ticket->id, 'file' => $link->file_id]) }}" class="link link--orange">
                                        <i class="fas fa-paperclip"></i> {{ $link->file->filename }}
                                    </a>
                                    @if($link->isMine())
                                        <a href="#" onclick="return false;" data-url="{{ route('account.tickets.attachments.remove', ['id' => $ticket->id, 'file' => $link->file_id]) }}" class="link link--red float-right remove-handle"><i class="far fa-trash-alt"></i></a>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                        <div class="actions text-center">
                            <span class="btn btn--orange fileinput-button">
                                <i class="glyphicon glyphicon-plus"></i>
                                <span>@lang('general.upload')</span>
                                <input id="fileupload" type="file" name="files[]" multiple disabled="disabled">
                            </span>
                            <div id="progress" class="progress d-none mt-3">
                                <div class="progress-bar progress-bar-success"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('footer_scripts')
    @parent
    <script src="{{ asset('assets/js/frontend/jquery.iframe-transport.js') }}" ></script>
    <script src="{{ asset('assets/js/frontend/jquery.fileupload.js') }}" ></script>
    <script>
        $(function () {
            $('#fileupload').fileupload({
                url: "{{ route('account.tickets.attachments.upload', ['id' => $ticket->id]) }}",
                dataType: 'json',
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
                    $.each(data.result.files, function (index, file) {
                        if (file.error) {
                            bootbox.alert(file.error);
                        } else {
                            $('<li><a href="' + file.url + '" class="link link--orange"><i class="fas fa-paperclip"></i>&nbsp;' + file.name + '</a></li>').appendTo('#files');
                        }
                    });
                },
                progressall: function (e, data) {
                    var progress = parseInt(data.loaded / data.total * 100, 10);
                    $('#progress .progress-bar').css(
                        'width',
                        progress + '%'
                    );
                }
            }).prop('disabled', !$.support.fileInput)
                .parent().addClass($.support.fileInput ? undefined : 'disabled');

            $('.attachments').on('click', '.remove-handle', function () {
                var btn = $(this);
                bootbox.confirm('Are you sure you want to delete attachment?', function (result) {
                    if (result) {
                        btn.closest('li').loading();
                        $.ajax(btn.data('url')).done(function () {
                            btn.closest('li').loading('stop');
                            btn.closest('li').remove();
                        }).fail(function (jqXHR, textStatus) {
                            bootbox.alert("Request failed: " + textStatus);
                        }).always(function () {
                            btn.closest('li').loading('stop');
                        });
                    }
                });

                return false;
            });
        });
    </script>
@stop
