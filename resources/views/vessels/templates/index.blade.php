@extends('vessels.documents.index')

@section('page_class')
    templates @parent
@stop

@section('header_styles')
    @parent
    <link href="{{ asset('assets/vendors/print-js/print.css') }}" type="text/css" rel="stylesheet">
@stop

@section('dashboard-content')
    <div class="dashboard-table-view">
        <div class="dashboard-btn-group-top dashboard-btn-group">
            <h2>@lang('general.template_documents_storage')</h2>
            <a class="btn btn--orange fileinput-button" href="#" role="button">
                <span>@lang('general.upload')</span>
                <input id="fileupload" type="file" name="file" disabled="disabled" data-url="{{ route('account.templates.upload') }}">
            </a>
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
                                                <a href="#" onclick="printJS({printable:'{{ route('account.templates.print', ['id' => $document->id]) }}', showModal:true}); return false;" target="_blank" class="document-print link link--orange">@lang('general.print')</a>
                                                <a href="#" class="document-details link link--orange" data-url="{{ route('account.templates.details', ['id' => $document->id]) }}">@lang('general.details')</a>
                                                <a href="{{ route('account.templates.download', ['id' => $document->id]) }}" class="document-download link link--orange">@lang('general.download')</a>
                                                <a href="#" class="document-delete link link--orange" data-url="{{ route('account.templates.remove', ['id' => $document->id]) }}">@lang('button.delete')</a>
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
    <script src="{{ asset('assets/vendors/print-js/print.min.js') }}"></script>
@stop