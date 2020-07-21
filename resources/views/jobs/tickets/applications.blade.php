@extends('layouts.dashboard-member')

@section('page_class')
    applications dashboard-jobs @parent
@stop

@section('dashboard-content')
    <div class="dashboard-table-view">
        <div class="dashboard-btn-group-top dashboard-btn-group">
            <h2>
                @lang('jobs.job_applications') for {{ $ticket->job->title }}
            </h2>
        </div>
        @parent
        @if($applications->count())
            <table id="job-applications-listing" class="dashboard-table table">
                <thead>
                <tr>
                    <th class="no-wrap" scope="col" width="1"></th>
                    <th scope="col"></th>
                    <th class="no-wrap text-center" scope="col" width="1">Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach($applications as $item)
                    <tr>
                        <td>
                            <span class="photo">
                                <img src="{{ $item->user->getThumb('53x53') }}" alt="{{ $item->title }}">
                            </span>
                        </td>
                        <td>
                            <h3>{{ $item->user->member_title }}</h3>
                        </td>
                        <td class="actions">
                            @if($item->thread)
                                <a href="{{ route('account.jobs.applicant.messages', ['ticket_id' => $ticket->id, 'id' => $item->id]) }}" class="mb-3 btn">
                                    @lang('message.messages')
                                    @if($item->thread->thread->isUnread(Sentinel::getUser()->getUserId()))
                                        <small title="Unread"><i class="fas fa-asterisk"></i></small>
                                    @endif
                                </a>
                            @endif
                            @if($item->id && is_null($item->job->applicant_id))
                                <button class="btn btn--orange choose-user" data-confirm-url="{{ route('account.jobs.applications.apply-user', ['ticket_id' => $ticket->id, 'id' => $item->id]) }}" data-confirm-message="Are you sure you want to apply {{ $item->user->member_title }} for {{ $item->job->title }} job?">@lang('jobs.choose_user')</button>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection

@section('footer_scripts')
    @parent
    <script>
        $(function () {
            $('#job-applications-listing').on('click', '.choose-user', function () {
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
@endsection