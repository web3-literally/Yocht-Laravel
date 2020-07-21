@extends('layouts.dashboard-member')

@section('page_class')
    messenger-messages applications dashboard-jobs @parent
@stop

@section('header_styles')
    @parent
    <link href="{{ asset('assets/css/frontend/jquery.fileupload.css') }}" rel="stylesheet"/>
@stop

@section('dashboard-content')
    <div class="dashboard-list-view">
        @if (is_null($ticket->job->applicant_id))
            <div class="inline-block float-right">
                <button class="btn btn--orange choose-user" data-confirm-url="{{ route('account.jobs.applications.apply-user', ['ticket_id' => $ticket->id, 'id' => $applicant->id]) }}" data-confirm-message="Are you sure you want to apply {{ $applicant->user->member_title }} for {{ $applicant->job->title }} job?">@lang('jobs.choose_user')</button>
            </div>
        @endif
        <h2>@lang('message.messages_with', ['name' => $thread->directUser()->full_name])</h2>
        <div class="container pl-0 pr-0">
            <div class="row">
                <div class="col-md-9">
                    <div class="thread">
                        <div class="dashboard-form-view">
                            @include('jobs.tickets.partials.form-message')
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
                            @foreach($applicant->attachments as $link)
                                <li>
                                    <a href="{{ route('account.jobs.applicant.attachments.download', ['ticket_id' => $ticket->id, 'id' => $applicant->id, 'file' => $link->file_id]) }}" class="link link--orange">
                                        <i class="fas fa-paperclip"></i> {{ $link->file->filename }}
                                    </a>
                                    <a href="#" onclick="return false;" data-url="{{ route('account.jobs.applicant.attachments.remove', ['ticket_id' => $ticket->id, 'id' => $applicant->id, 'file' => $link->file_id]) }}" class="link link--red float-right remove-handle"><i class="far fa-trash-alt"></i></a>
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
    <script>
        $(function () {
            $('.choose-user').on('click', function () {
                var btn = $(this);
                bootbox.confirm(btn.data('confirm-message'), function (result) {
                    if (result) {
                        $('body').loading();
                        $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            'url': btn.data('confirm-url'),
                            'type': 'POST',
                            'success': function (data) {
                                if (data.success) {
                                    if (data.redirect) {
                                        window.location = data.redirect;
                                    }
                                    if (data.message) {
                                        bootbox.alert(data.message, function () {
                                            window.location.reload();
                                        });
                                    }
                                } else {
                                    bootbox.alert(data.message);
                                }
                            },
                            'error': function (request, error) {
                                bootbox.alert('There was an error. Please Try again later.');
                            },
                            'complete': function () {
                                $('body').loading('stop');
                            }
                        });
                    }
                });
                return false;
            });
        });
    </script>
    <script src="{{ asset('assets/js/frontend/jquery.iframe-transport.js') }}"></script>
    <script src="{{ asset('assets/js/frontend/jquery.fileupload.js') }}"></script>
    <script>
        $(function () {
            $('#fileupload').fileupload({
                url: "{{ route('account.jobs.applicant.attachments.upload', ['ticket_id' => $ticket->id, 'id' => $applicant->id]) }}",
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
                            var download = $('<a href="' + file.url + '" class="link link--orange"><i class="fas fa-paperclip"></i>&nbsp;' + file.name + '</a>');
                            var remove = $('<a href="#" onclick="return false;" data-url="' + file.url + '" class="link link--red float-right remove-handle"><i class="far fa-trash-alt"></i></a>');
                            var li = $('<li></li>');
                            li.append(download);
                            li.append(remove);
                            li.appendTo('#files');
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
@endsection
