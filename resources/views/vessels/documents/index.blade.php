@extends('layouts.dashboard-member')

@section('page_class')
    dashboard-vessels-documents @parent
@stop

@section('header_styles')
    @parent
    <link href="{{ asset('assets/css/frontend/jquery.fileupload.css') }}" rel="stylesheet"/>
    <link href="{{ asset('assets/vendors/select2/css/select2.min.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('assets/vendors/select2/css/select2-bootstrap.css') }}" rel="stylesheet">
@stop

@section('dashboard-content')
    <div class="dashboard-table-view">
        <div class="dashboard-btn-group-top dashboard-btn-group">
            <h2>@lang('general.documents_storage')</h2>
            <a class="btn btn--orange fileinput-button" href="#" role="button">
                <span>@lang('general.upload')</span>
                <input id="fileupload" type="file" name="file" disabled="disabled" data-url="{{ route('account.documents.upload') }}">
            </a>
            @include('vessels.documents._search')
        </div>
        <div id="documents-container">
            <div class="results">
                @if($documents->count())
                    <div class="container-fluid p-0">
                        <div class="row">
                            <div class="col-9">
                                <table class="dashboard-table table">
                                    <thead>
                                    <tr>
                                        <th scope="col" width="1"></th>
                                        <th scope="col"></th>
                                        <th scope="col">Modified</th>
                                        <th class="no-wrap text-center" scope="col" width="1">Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($documents as $document)
                                        <tr class="file">
                                            <td>
                                                <i class="far fa-file"></i>
                                            </td>
                                            <td>
                                                {{ $document->file->filename }}
                                            </td>
                                            <td>
                                                {{ $document->file->updated_at->diffForHumans() }}
                                            </td>
                                            <td class="actions no-wrap">
                                                @if($document->file->mime == 'application/pdf')
                                                    <a href="{{ route('account.documents.view', ['id' => $document->id]) }}" target="_blank" class="document-view link link--orange">@lang('general.view')</a>
                                                @endif
                                                <a href="#" class="document-details link link--orange" data-url="{{ route('account.documents.details', ['id' => $document->id]) }}">@lang('general.details')</a>
                                                <a href="{{ route('account.documents.download', ['id' => $document->id]) }}" class="document-download link link--orange">@lang('general.download')</a>
                                                <a href="#" class="document-delete link link--orange" data-url="{{ route('account.documents.remove', ['id' => $document->id]) }}">@lang('button.delete')</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-3">
                                <div id="documents-details">
                                    <div class="documents-details">
                                        <h4>@lang('general.details')</h4>
                                        <p class="text-center">Total {{ $documents->total() }} files</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{ $documents->links() }}
                @else
                    <div class="alert alert-info">@lang('vessels.no_documents')</div>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('footer_scripts')
    @parent
    <script src="{{ asset('assets/js/frontend/jquery.iframe-transport.js') }}"></script>
    <script src="{{ asset('assets/js/frontend/jquery.fileupload.js') }}"></script>
    <script src="{{ asset('assets/js/frontend/jquery.fileupload-process.js') }}"></script>
    <script src="{{ asset('assets/js/frontend/jquery.fileupload-validate.js') }}"></script>
    <script src="{{ asset('assets/vendors/select2/js/select2.js') }}" type="text/javascript"></script>
    <script>
        $(function () {
            var currentUrl = "{{ route(Request::route()->getName(), Request::route()->parameters) }}";
            var tableView = $('.dashboard-table-view');
            var reloadDocuments = function (url) {
                tableView.loading({
                    'message': 'Loading...'
                });
                if (typeof (url) == 'undefined') {
                    url = currentUrl;
                } else {
                    currentUrl = url;
                }
                $('#documents-container').load(url + " #documents-container .results", function () {
                    tableView.loading('stop');
                });
                window.history.pushState({}, null, url);
            };
            var loadDocumentDetails = function (url) {
                var detailsView = tableView.find('#documents-details');

                var onChangePermissions = function(e) {
                    var data = e.params.data;
                    //detailsView.find(".documents-permissions").loading();
                    $.ajax({
                        method: 'GET',
                        url: detailsView.find("#documents-permissions-input").data('store-url'),
                        data: {
                            'grant': data.selected ? 'read' : 'none',
                            'member_id': data.id
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            if (!errorThrown) {
                                errorThrown = 'An unknown error has occurred';
                            }
                            bootbox.alert(errorThrown);
                        },
                        success: function (data, textStatus, jqXHR) {
                            //
                        },
                        complete: function() {
                            //detailsView.find(".documents-permissions").loading('stop');
                        }
                    });
                };

                detailsView.loading();
                detailsView.load(url + " .documents-details", function () {
                    detailsView.find("#documents-permissions-input").select2({
                        placeholder: "select a member",
                        theme:"bootstrap"
                    }).on('select2:select', onChangePermissions).on('select2:unselect', onChangePermissions);
                    detailsView.loading('stop');
                });
            };
            tableView.on('click', '.document-details', function () {
                loadDocumentDetails($(this).data('url'));
                return false;
            });
            tableView.on('click', '.pagination .page-link', function () {
                $(this).blur();
                reloadDocuments($(this).attr('href'));
                return false;
            });
            tableView.on('click', '.document-delete', function () {
                var btn = $(this);
                if (confirm('Are you sure you want to delete document?')) {
                    tableView.loading({
                        'message': 'Deleting...'
                    });
                    $.ajax({
                        url: btn.data('url'),
                        error: function (jqXHR, textStatus, errorThrown) {
                            tableView.loading('stop');
                            if (!errorThrown) {
                                errorThrown = 'An unknown error has occurred';
                            }
                            bootbox.alert(errorThrown);
                        },
                        success: function (data, textStatus, jqXHR) {
                            reloadDocuments($(this).attr('href'));
                        },
                    });
                }
                return false;
            });
            $('#fileupload').fileupload({
                url: $('#fileupload').data('url'),
                dataType: 'json',
                autoUpload: true,
                maxFileSize: 40000000, // 40 MB
                maxNumberOfFiles: 1,
                start: function (e) {
                    tableView.loading({
                        'message': 'Uploading...'
                    });
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
                        tableView.loading('stop');
                        bootbox.alert(file.error);
                    } else {
                        reloadDocuments();
                    }
                },
            }).prop('disabled', !$.support.fileInput)
                .parent().addClass($.support.fileInput ? undefined : 'disabled');
        });
    </script>
@stop
