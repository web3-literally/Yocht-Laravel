@extends('admin/layouts/default')

{{-- Page Title --}}
@section('title')
    Log view
    @parent
@stop
@section('header_styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/pages/log_viewer.css') }}">
@stop
@section('content')
    <section class="content-header">
        <h1>Logs</h1>
        <ol class="breadcrumb">
            <li>
                <a href="{{ route('admin.dashboard') }}"> <i class="livicon" data-name="home" data-size="16" data-color="#000"></i>
                    @lang('general.dashboard')
                </a>
            </li>
            <li><a href="#">Logger View</a></li>
            <li class="active">show</li>
        </ol>
    </section>


    <!--main content-->
<section class="content">
    <div class="row">
        <div class="col-12 col-lg-12 col-md-12">

            <div class="card panel-primary">
                <div class="card-heading clearfix">
                    <h4 class="card-title float-left">
                        <i class="livicon" data-name="edit" data-size="16" data-loop="true" data-c="#fff" data-hc="white"></i> Log [{{ $log->date }}]
                    </h4>

                </div>
                <div class="card-body">
                    <div class="row">
                        {{--<div class="col-md-2">--}}
                            {{--@include('log-viewer::_partials.menu')--}}
                        {{--</div>--}}
                        <div class="col-md-12">
                            {{-- Log Details --}}
                            {{--<div class="card panel-default">--}}
                                {{--<div class="card-heading">--}}
                                    {{--Log info :--}}

                                    {{--<div class="group-btns pull-right">--}}
                                        {{--<a href="{{ route('log-viewer::logs.download', [$log->date]) }}" class="btn btn-xs btn-success">--}}
                                            {{--<i class="fa fa-download"></i> DOWNLOAD--}}
                                        {{--</a>--}}
                                        {{--<a href="#delete-log-modal" class="btn btn-xs btn-danger" data-toggle="modal">--}}
                                            {{--<i class="fa fa-trash-o"></i> DELETE--}}
                                        {{--</a>--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                                {{--<div class="table-responsive-lg table-responsive-sm table-responsive-md">--}}
                                    {{--<table class="table table-condensed">--}}
                                        {{--<thead>--}}
                                        {{--<tr>--}}
                                            {{--<td>File path :</td>--}}
                                            {{--<td colspan="5">{{ $log->getPath() }}</td>--}}
                                        {{--</tr>--}}
                                        {{--</thead>--}}
                                        {{--<tbody>--}}
                                        {{--<tr>--}}
                                            {{--<td>Log entries : </td>--}}
                                            {{--<td>--}}
                                                {{--<span class="label label-primary">{{ $entries->total() }}</span>--}}
                                            {{--</td>--}}
                                            {{--<td>Size :</td>--}}
                                            {{--<td>--}}
                                                {{--<span class="label label-primary">{{ $log->size() }}</span>--}}
                                            {{--</td>--}}
                                            {{--<td>Created at :</td>--}}
                                            {{--<td>--}}
                                                {{--<span class="label label-primary">{{ $log->createdAt() }}</span>--}}
                                            {{--</td>--}}
                                            {{--<td>Updated at :</td>--}}
                                            {{--<td>--}}
                                                {{--<span class="label label-primary">{{ $log->updatedAt() }}</span>--}}
                                            {{--</td>--}}
                                        {{--</tr>--}}
                                        {{--</tbody>--}}
                                    {{--</table>--}}
                                {{--</div>--}}
                                {{--<div class="panel-footer">--}}
                                    {{-- Search --}}
                                    {{--<form action="{{ route('log-viewer::logs.search', [$log->date, $level]) }}" method="GET">--}}
                                        {{--<div class=form-group">--}}
                                            {{--<div class="input-group">--}}
                                                {{--<input id="query" name="query" class="form-control"  value="{!! request('query') !!}" placeholder="typing something to search">--}}
                                                {{--<span class="input-group-btn">--}}
                                    {{--@if (request()->has('query'))--}}
                                                        {{--<a href="{{ route('log-viewer::logs.show', [$log->date]) }}" class="btn btn-default"><span class="glyphicon glyphicon-remove"></span></a>--}}
                                                    {{--@endif--}}
                                                    {{--<button id="search-btn" class="btn btn-primary"><span class="glyphicon glyphicon-search"></span></button>--}}
                                {{--</span>--}}
                                            {{--</div>--}}
                                        {{--</div>--}}
                                    {{--</form>--}}
                                {{--</div>--}}
                            {{--</div>--}}

                            {{-- Log Entries --}}
                            <div class="card panel-default">
                                @if ($entries->hasPages())
                                    <div class="card-heading">
                                        {!! $entries->appends(compact('query'))->render() !!}

                                        <span class="label label-info pull-right">
                            Page {!! $entries->currentPage() !!} of {!! $entries->lastPage() !!}
                        </span>
                                    </div>
                                @endif
                                <div class="able-responsive-lg table-responsive-md table-responsive-sm table-responsive">
                                    <table id="entries" class="table table-condensed" width="100%">
                                        <thead>
                                        <tr>
                                            <th>ENV</th>
                                            <th>Level</th>
                                            <th >Time</th>
                                            <th>Header</th>
                                            <th class="text-right">Actions</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @forelse($entries as $key => $entry)
                                            <tr>
                                                <td>
                                                    <span class="label label-env">{{ $entry->env }}</span>
                                                </td>
                                                <td>
                                        <span class="level level-{{ $entry->level }}">
                                            {!! $entry->level() !!}
                                        </span>
                                                </td>
                                                <td>
                                        <span class="label label-default">
                                            {{ $entry->datetime->format('H:i:s') }}
                                        </span>
                                                </td>
                                                <td>
                                                    <p>{{ $entry->header }}</p>
                                                </td>
                                                <td class="text-right">
                                                    @if ($entry->hasStack())
                                                        <a class="btn btn-sm btn-default" role="button" data-toggle="collapse" href="#log-stack-{{ $key }}" aria-expanded="false" aria-controls="log-stack-{{ $key }}">
                                                            <i class="fa fa-toggle-on"></i> Stack
                                                        </a>
                                                    @endif
                                                </td>
                                            </tr>
                                            @if ($entry->hasStack())
                                                <tr>
                                                    <td colspan="5" class="stack">
                                                        <div class="stack-content collapse" id="log-stack-{{ $key }}">
                                                            {!! $entry->stack() !!}
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endif
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center">
                                                    <span class="label label-default">{{ trans('log-viewer::general.empty-logs') }}</span>
                                                </td>
                                            </tr>
                                        @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                    <div class="table-responsive-lg table-responsive-md table-responsive-sm table-responsive">
                                        <table id="entries" class="table table-condensed" width="100%">
                                            <thead>
                                            <tr>
                                                <th>ENV</th>
                                                <th>Level</th>
                                                <th >Time</th>
                                                <th>Header</th>
                                                <th class="text-right">Actions</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @forelse($entries as $key => $entry)
                                                <tr>
                                                    <td>
                                                        <span class="label label-env">{{ $entry->env }}</span>
                                                    </td>
                                                    <td>
                                        <span class="level level-{{ $entry->level }}">
                                            {!! $entry->level() !!}
                                        </span>
                                                    </td>
                                                    <td>
                                        <span class="label label-default">
                                            {{ $entry->datetime->format('H:i:s') }}
                                        </span>
                                                    </td>
                                                    <td>
                                                        <p>{{ $entry->header }}</p>
                                                    </td>
                                                    <td class="text-right">
                                                        @if ($entry->hasStack())
                                                            <a class="btn btn-sm btn-default" role="button" data-toggle="collapse" href="#log-stack-{{ $key }}" aria-expanded="false" aria-controls="log-stack-{{ $key }}">
                                                                <i class="fa fa-toggle-on"></i> Stack
                                                            </a>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @if ($entry->hasStack())
                                                    <tr>
                                                        <td colspan="5" class="stack">
                                                            <div class="stack-content collapse" id="log-stack-{{ $key }}">
                                                                {!! $entry->stack() !!}
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endif
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center">
                                                        <span class="label label-default">{{ trans('log-viewer::general.empty-logs') }}</span>
                                                    </td>
                                                </tr>
                                            @endforelse
                                            </tbody>
                                        </table>
                                    </div>

                                @if ($entries->hasPages())
                                    <div class="panel-footer">
                                        {!! $entries->appends(compact('query'))->render() !!}

                                        <span class="label label-info float-right">
                            Page {!! $entries->currentPage() !!} of {!! $entries->lastPage() !!}
                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <a class="btn btn-primary my-3" href="{{ URL::to('admin/log_viewers/logs') }}">
                Back
            </a>
        </div>
    </div>
</section>
    <!--main content ends-->

    <!--Delete Modal-->
    <div id="delete-log-modal" class="modal fade">
        <div class="modal-dialog">
            <form id="delete-log-form" action="{{ URL::to('admin/log_viewers/logs/delete') }}" method="POST">
                <input type="hidden" name="_method" value="DELETE">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="date" value="{{ $log->date }}">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">DELETE LOG FILE</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to <span class="label label-danger">DELETE</span> this log file <span class="label label-primary">{{ $log->date }}</span> ?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-default pull-left" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-sm btn-danger" data-loading-text="Loading&hellip;">DELETE FILE</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection



@section('scripts')
    <script>
        $(function () {
            var deleteLogModal = $('div#delete-log-modal'),
                deleteLogForm  = $('form#delete-log-form'),
                submitBtn      = deleteLogForm.find('button[type=submit]');

            deleteLogForm.on('submit', function(event) {
                event.preventDefault();
                submitBtn.button('loading');

                $.ajax({
                    url:      $(this).attr('action'),
                    type:     $(this).attr('method'),
                    dataType: 'json',
                    data:     $(this).serialize(),
                    success: function(data) {
                        submitBtn.button('reset');
                        if (data.result === 'success') {
                            deleteLogModal.modal('hide');
                            location.replace("{{ URL::to('admin/log_viewers/logs/delete') }}");
                        }
                        else {
                            alert('OOPS ! This is a lack of coffee exception !')
                        }
                    },
                    error: function(xhr, textStatus, errorThrown) {
                        alert('AJAX ERROR ! Check the console !');
                        console.error(errorThrown);
                        submitBtn.button('reset');
                    }
                });

                return false;
            });

            @unless (empty(log_styler()->toHighlight()))
            $('.stack-content').each(function() {
                var $this = $(this);
                var html = $this.html().trim()
                    .replace(/({!! join(log_styler()->toHighlight(), '|') !!})/gm, '<strong>$1</strong>');

                $this.html(html);
            });
            @endunless
        });
    </script>
@endsection
