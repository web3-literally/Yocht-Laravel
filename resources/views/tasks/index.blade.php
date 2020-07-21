@extends('layouts.dashboard-member')

@section('header_styles')
    @parent
    <link href="{{ asset('assets/vendors/select2/css/select2.min.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('assets/vendors/select2/css/select2-bootstrap.css') }}" rel="stylesheet">
@endsection

@section('page_class')
    dashboard-tasks @parent
@stop

@section('dashboard-content')
    <div class="dashboard-table-view">
        <div class="dashboard-btn-group-top dashboard-btn-group">
            <h2>@lang('tasks.tasks')</h2>
            <a class="btn btn--orange" href="{{ route('account.tasks.create') }}" role="button">Add a New Task</a>
        </div>
        <div class="top-panel">
            <div class="column d-flex">
                <div id="add-column" class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle btn--orange" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        @lang('button.add') a Column
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <div class="task-attributes-list">
                            @foreach($attributes as $attribute)
                                <label class="dropdown-item" for="{{ $attribute->attribute_code }}-field">
                                    <input id="{{ $attribute->attribute_code }}-field" type="checkbox" class="unstyle" value="{{ $attribute->attribute_code }}" {!! in_array($attribute->attribute_code, $columns) ? 'checked="checked"' : '' !!}>
                                    <a href="#" data-action="{{ route('account.tasks.attributes.remove', ['attribute' => $attribute->attribute_code]) }}" class="remove-field link link--red float-right"><i class="far fa-trash-alt"></i></a>
                                    <span class="mr-4">{{ $attribute->frontend_label }}</span>
                                </label>
                            @endforeach
                        </div>
                        <div class="placeholder"></div>
                        @if($attributes->count())
                            <div class="dropdown-divider"></div>
                        @endif
                        <a class="dropdown-item" href="#" data-toggle="modal" data-target="#modal-new-column">New
                            Column</a>
                    </div>
                </div>
                <div class="filter-section">
                    {!! Form::open(['url' => url()->current(), 'method' => 'GET', 'class' => 'form-style', 'id' => 'filter-form']) !!}
                    @php
                        if ($id = request('assigned_to')) {
                            if (is_numeric($id)) {
                                $member = \App\User::getModel()->findOrFail($id);
                                $selectedMember = [$member->id => "{$member->member_title} ({$member->account_type_title})"];
                            } else {
                                if ($role = Sentinel::findRoleBySlug($id)) {
                                    $selectedMember = [$role->slug => "{$role->name}"];
                                }
                            }
                        }
                    @endphp
                    <div class="d-flex justify-content-start">
                        {!! Form::select('set_as', $setAsList, request('set_as'), ['class' => 'd-inline-block form-control', 'placeholder' => 'Set As', 'id' => 'set_as']) !!}
                        {!! Form::select('priority', $priorityList, request('priority'), ['class' => 'd-inline-block form-control', 'placeholder' => 'Priority', 'id' => 'priority']) !!}
                        {!! Form::select('assigned_to', $selectedMember ?? [], request('assigned_to'), ['class' => 'd-inline-block form-control', 'placeholder' => 'Assigned to', 'id' => 'assigned_to']) !!}
                    </div>
                    <div class="search-section">
                        {!! Form::text('search', request('search'), ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Search', 'id' => 'search']) !!}
                    </div>
                    {!! Form::hidden('status', null) !!}
                    {!! Form::hidden('order', null) !!}
                    {!! Form::close() !!}
                </div>
            </div>
            <div class="column">
                <div class="status-section">
                    <div class="btn-group" role="group" aria-label="">
                        <a href="{{ app('request')->fullUrlWithQuery(['status' => '']) }}" class="btn btn--orange">Owned</a>
                        <a href="{{ app('request')->fullUrlWithQuery(['status' => 'shared']) }}" class="btn btn--orange">Shared
                            ({{ \App\Models\Tasks\Task::sharedToMe($related_member_id)->get()->count() }})</a>
                        <a href="{{ app('request')->fullUrlWithQuery(['status' => 'acknowledge']) }}" class="btn btn--orange">Acknowledge
                            ({{ \App\Models\Tasks\Task::acknowledge($related_member_id)->get()->count() }})</a>
                        <a href="{{ app('request')->fullUrlWithQuery(['status' => 'upcoming']) }}" class="btn btn--orange">Upcoming
                            ({{ \App\Models\Tasks\Task::upcoming($related_member_id)->get()->count() }})</a>
                        <a href="{{ app('request')->fullUrlWithQuery(['status' => 'snoozed']) }}" class="btn btn--orange">Snoozed
                            ({{ \App\Models\Tasks\Task::snoozed($related_member_id)->get()->count() }})</a>
                        <a href="{{ app('request')->fullUrlWithQuery(['status' => 'overdue']) }}" class="btn btn--orange">Overdue
                            ({{ \App\Models\Tasks\Task::overdue($related_member_id)->get()->count() }})</a>
                        <a href="{{ app('request')->fullUrlWithQuery(['status' => 'completed']) }}" class="btn btn--orange">Completed</a>
                    </div>
                </div>
            </div>
        </div>
        @if($tasks->count())
            <div class="data-section overflow-auto">
                <table id="data-section-table" class="dashboard-table table">
                    <thead>
                    <tr>
                        <th class="no-wrap text-center" scope="col" width="200px">Actions</th>
                        <th class="no-wrap" scope="col" width="1">Set As</th>
                        <th class="no-wrap" scope="col" width="1">Priority</th>
                        <th class="no-wrap" scope="col" width="1">Date issued</th>
                        <th class="no-wrap" scope="col" width="1">Due date</th>
                        <th scope="col">Notes</th>
                        @if($additional_attributes->count())
                            @foreach($additional_attributes as $attribute)
                                <th class="no-wrap" scope="col" width="1">{{ $attribute->frontend_label }}</th>
                            @endforeach
                        @endif
                        <th class="no-wrap" scope="col" width="1">Assigned to</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($tasks as $task)
                        <tr>
                            <td class="actions">
                                @include('tasks._actions')
                            </td>
                            <td class="no-wrap">
                                {{ $task->set_as_label }}
                            </td>
                            <td class="no-wrap">
                                <span class="counter priority-badge {{ $task->priority }}">{{ $task->priority_label }}</span>
                            </td>
                            <td class="no-wrap">
                                {{ $task->created_at->toFormattedDateString() }}
                            </td>
                            <td class="no-wrap">
                                {{ $task->due_date_at ? \Carbon\Carbon::parse($task->due_date_at)->toFormattedDateString() : '' }}
                            </td>
                            <td>
                                {!! HtmlTruncator::truncate($task->description, 26) !!}
                            </td>
                            @if($additional_attributes->count())
                                @foreach($additional_attributes as $attribute)
                                    <td class="no-wrap">
                                        {{ $task->getAttributeValue($attribute->attribute_code) }}
                                    </td>
                                @endforeach
                            @endif
                            <td class="no-wrap">
                                @if($task->assigned_to_id)
                                    {{ $task->assigned_to->member_title }}<br>
                                    {{ $task->assigned_to->account_type_title }}
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            {{ $tasks->links() }}
        @else
            <div class="alert alert-info">@lang('tasks.no_tasks')</div>
        @endif
    </div>

    <div class="modal fade" id="modal-share-to" role="dialog" aria-labelledby="" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Share to</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    {!! Form::open(['route' => 'account.tasks.share', 'method' => 'POST', 'class' => 'form-style', 'id' => 'share-form']) !!}
                    {!! Form::select('share_to_id', [], null, ['class' => 'form-control', 'id' => 'share_to']) !!}
                    {!! Form::hidden('id', null) !!}
                    {!! Form::close() !!}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary">Share</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-new-column" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">New Column</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    {!! Form::open(['url' => route('account.tasks.attributes.store'), 'method' => 'POST', 'class' => 'form-style', 'id' => 'new-attribute-form']) !!}
                    <div class="form-row">
                        <div class="col-md-12">
                            <div class="form-group {{ $errors->first('title', 'has-error') }}">
                                {!! Form::label('title', 'Title *', ['for' => 'title']) !!}
                                {!! Form::text('title', null, ['class' => 'form-control', 'autocomplete' => 'off', 'id' => 'title']) !!}
                                {!! $errors->first('title', '<span class="help-block">:message</span>') !!}
                            </div>
                            <div class="form-group {{ $errors->first('type', 'has-error') }}">
                                {!! Form::label('type', 'Type *', ['for' => 'type']) !!}
                                {!! Form::select('type', ['text' => 'Text', 'select' => 'Dropdown'], null, ['class' => 'form-control w-50', 'id' => 'type']) !!}
                                <div class="dropdown-options d-none">
                                    <array-input></array-input>
                                </div>
                                {!! $errors->first('type', '<span class="help-block">:message</span>') !!}
                            </div>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary">Save</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer_scripts')
    @parent
    <script src="{{ asset('assets/vendors/select2/js/select2.js') }}" type="text/javascript"></script>
    <script>
        var reload = function () {
            var url = window.location.href;
            $('.data-section').loading();
            $.ajax({
                url: url,
                data: {
                    '_token': "{{ csrf_token() }}"
                },
                success: function (result) {
                    var html = $(result);

                    var list = html.find('#add-column .task-attributes-list').clone();
                    $('#add-column .task-attributes-list').remove();
                    $('#add-column .dropdown-menu .placeholder').prepend(list);

                    var table = html.find('#data-section-table').clone();
                    $('.data-section').find('#data-section-table').remove();
                    $('.data-section').append(table);
                },
                complete: function () {
                    $('.data-section').loading('stop');
                }
            });
        };

        $("#add-column").on('change', 'input[type=checkbox]', function () {
            var data = $(this).closest('.dropdown').find('input[type=checkbox]:checked').map(function () {
                return $(this).val();
            }).get();
            setCookie('tasks_additional_columns', JSON.stringify(data));
            reload();
        });
        $("#add-column").on('click', '.remove-field', function () {
            var btn = $(this);

            bootbox.confirm('Are you sure you want to delete "' + btn.next('span').text() + '" attribute?', function (result) {
                if (result) {
                    $.ajax(btn.data('action'), {
                        method: 'GET',
                        dataType: 'json'
                    }).done(function (response) {
                        bootbox.alert(response.message);
                        btn.closest('.dropdown-item').find('input[type=checkbox]').prop('checked', false);
                        var data = $('#add-column.dropdown input[type=checkbox]:checked').map(function () {
                            return $(this).val();
                        }).get();
                        setCookie('tasks_additional_columns', JSON.stringify(data));
                        reload();
                    }).error(function (jqXHR, textStatus) {
                        var response = JSON.parse(jqXHR.responseText);
                        bootbox.alert("Request failed: " + textStatus);
                    }).complete(function () {
                        //
                    });
                }
            });

            return false;
        });

        $('#filter-form select').on('change', function () {
            $(this).closest('form').submit();
        });
        $("#assigned_to").select2({
            ajax: {
                url: "{{ route('account.tasks.members.data', ['with' => 'position']) }}",
                dataType: 'json',
                data: function (params) {
                    var query = {
                        search: params.term
                    };
                    return query;
                }
            },
            minimumInputLength: 0,
            templateResult: function (item) {
                if (item.id === '' || item.loading) {
                    return $('<span>' + item.text + '</span>');
                }
                var el = $(
                    '<span class="select-member-item">' + (item.thumb ? '<img src="' + item.thumb + '">' : '') + '<span class="name">' + item.text + '</span></span>'
                );
                return el;
            },
            placeholder: "Assigned to",
            theme: "bootstrap",
            width: '100%',
            allowClear: true
        }).on("select2:unselecting", function (e) {
            $(this).data('state', 'unselected');
        }).on("select2:open", function (e) {
            if ($(this).data('state') === 'unselected') {
                $(this).removeData('state');
                var self = $(this);
                setTimeout(function () {
                    self.select2('close');
                }, 1);
            }
        });

        $('#modal-share-to').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var modal = $(this);

            if (button.data('id')) {
                modal.find('form input[name=id]').val(button.data('id'));
            }
        });
        $('#share-form').on('submit', function () {
            var form = $(this);
            $('#modal-share-to').modal('hide');
            $.ajax(form.attr('action'), {
                method: 'POST',
                dataType: 'json',
                data: form.serialize()
            }).done(function (data) {
                bootbox.alert(data);
            }).fail(function (jqXHR, textStatus) {
                bootbox.alert("Request failed: " + textStatus, function () {
                    $('#modal-share-to').modal('show');
                });
            });

            return false;
        });
        $('#modal-share-to').find('.modal-footer .btn-primary').on('click', function () {
            $('#share-form').submit();
        });
        $("#share_to").select2({
            ajax: {
                url: "{{ route('account.tasks.members.data') }}",
                dataType: 'json',
                data: function (params) {
                    var query = {
                        search: params.term
                    };
                    return query;
                }
            },
            minimumInputLength: 1,
            templateResult: function (item) {
                if (item.id === '' || item.loading) {
                    return $('<span>' + item.text + '</span>');
                }
                var el = $(
                    '<span class="select-member-item">' + (item.thumb ? '<img src="' + item.thumb + '">' : '') + '<span class="name">' + item.text + '</span></span>'
                );
                return el;
            },
            placeholder: "Select a member",
            theme: "bootstrap",
            width: '100%'
        });
        $('.data-section').on('click', '.btn-snoozed-for', function () {
            var btn = $(this);
            $.ajax(btn.data('url'), {
                method: 'GET',
                dataType: 'json'
            }).done(function (data) {
                bootbox.alert(data);
                reload();
            }).fail(function (jqXHR, textStatus) {
                bootbox.alert("Request failed: " + textStatus);
            });
        });
        $('.data-section').on('click', '.btn-acknowledge, .btn-completed, .btn-cancelled', function () {
            var btn = $(this);
            $.ajax(btn.data('url'), {
                method: 'GET',
                dataType: 'json'
            }).done(function (data) {
                bootbox.alert(data);
                reload();
            }).fail(function (jqXHR, textStatus) {
                bootbox.alert("Request failed: " + textStatus);
            });

            return false;
        });

        $('#new-attribute-form').on('submit', function () {
            var form = $(this);
            $('#modal-new-column').loading();

            form.find('.has-error').removeClass('has-error');
            form.find('.help-block').remove();

            $.ajax(form.attr('action'), {
                method: 'POST',
                dataType: 'json',
                data: form.serialize()
            }).done(function (data) {
                $('#modal-new-column').modal('hide');
                reload();
            }).error(function (jqXHR, textStatus) {
                var data = JSON.parse(jqXHR.responseText);
                if (data.errors) {
                    $.each(data.errors, function (key, value) {
                        if (key === 'options')
                            key = 'type';

                        var parts = key.split('.', 2);
                        var input = form.find(parts.length > 1 ? '[name=' + parts[0] + '\\[' + parts[1] + '\\]]' : '[name=' + parts[0] + ']');

                        input.closest('.form-group').addClass('has-error');
                        input.closest('.form-group').append('<span class="help-block d-block">' + value[0] + '</span>');
                    });
                } else {
                    bootbox.alert("Request failed: " + textStatus);
                }
            }).complete(function () {
                $('#modal-new-column').loading('stop');
            });

            return false;
        });
        $('#new-attribute-form').find('#type').on('change', function() {
            if ($(this).val() == 'select') {
                $(this).next('.dropdown-options').removeClass('d-none');
            } else {
                $(this).next('.dropdown-options').addClass('d-none');
            }
        });
        $('#modal-new-column').find('.modal-footer .btn-primary').on('click', function () {
            $('#new-attribute-form').submit();
        });
    </script>
@endsection