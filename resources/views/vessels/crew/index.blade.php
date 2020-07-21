@extends('layouts.dashboard-member')

@section('page_class')
    dashboard-vessels-crew @parent
@stop

@section('header_styles')
    @parent
    <link href="{{ asset('assets/css/frontend/spectrum.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('assets/css/frontend/flag-icon.css') }}" rel="stylesheet" />
@endsection

@section('dashboard-content')
    <div class="dashboard-table-view">
        <div class="dashboard-btn-group-top dashboard-btn-group">
            <h2>@lang('vessels.crew') <small>({{ count($assigned) }}/{{ $vesselTeamSlotsCount }})</small></h2>
            <a class="btn btn--orange" href="{{ route('account.boat.crew.create') }}" role="button">@lang('general.add_profile')</a>
        </div>
        @if($crew->count())
            <table class="dashboard-table table">
                <thead>
                <tr>
                    <th scope="col"></th>
                    <th class="no-wrap text-center" scope="col" width="230">Actions</th>
                </tr>
                </thead>
                <tbody>
                    @foreach($crew as $member)
                        <tr>
                            <td style="border-left-width: 7px; border-left-style: solid; border-left-color: {{ $member->listing_color }};">
                                <div class="container-fluid">
                                    <div class="row">
                                        <div class="col-md-1">
                                            <img class="rounded-avatar" src="{{ $member->getProfileThumb('89x89') }}" alt="{{ $member->full_name }}">
                                        </div>
                                        <div class="col-md-5">
                                            <h3 class="mb-2">
                                                {{ $member->full_name }}
                                            </h3>
                                            <span class="category label">{{ $member->position_label }}</span><br>
                                            <span><small><i class="fas fa-phone"></i> {{ $member->phone }}</small></span>
                                            <span class="ml-2"><small><i class="fas fa-envelope"></i> {{ $member->email }}</small></span>
                                            @if ($member->country)
                                                <span class="ml-2"><span class="flag-icon flag-icon-{{ strtolower($member->country) }}"></span> <span>{{ $member->country_label }}</span></span>
                                            @endif
                                        </div>
                                        <div class="col-md-3">
                                            @if($member->profile->file_id)
                                                <a href="{{ route('account.boat.crew.view.cv', ['member_id' => $member->id]) }}" class="link link--orange">{{ $member->profile->file->filename }}</a>
                                            @endif
                                        </div>
                                        <div class="col-md-2">
                                            @if($member->vessel)
                                                <a href="{{ route('account.dashboard', ['related_member_id' => $member->vessel->user->id]) }}" class="category label">{{ $member->vessel->name }}</a>
                                            @endif
                                        </div>
                                        <div class="col-md-1">
                                            @if(Sentinel::getUser()->hasAccess('crew.colors'))
                                                {{ Form::hidden('color', $member->profile->color, ['class' => 'color-input', 'data-id' => $member->id]) }}
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="actions">
                                @if(($member->isCaptainAccount() && Sentinel::getUser()->isMemberOwnerAccount()) || !$member->isCaptainAccount())
                                    @if (in_array($member->id, $assigned))
                                        <a href="{{ route('account.boat.crew.unassign', ['member_id' => $member->id]) }}" onclick="return confirm('Are you sure to unassign the &quot;'+ $(this).data('title') +'&quot; from vessel?')" class="btn" data-title="{{ $member->full_name }}">Unassign</a>
                                    @else
                                        <a href="{{ route('account.boat.crew.assign', ['member_id' => $member->id]) }}" class="btn" data-title="{{ $member->full_name }}">Assign</a>
                                    @endif
                                    @if(Sentinel::getUser()->hasAccess('crew.manage'))
                                        <br>
                                        <a href="{{ route('account.crew.profile', ['user_id' => $member->id]) }}" class="btn">@lang('general.manage_profile')</a>
                                        <a href="{{ route('account.boat.crew.remove', ['member_id' => $member->id]) }}" onclick="return confirm('Are you sure to delete the &quot;'+ $(this).data('title') +'&quot; profile?')" class="btn" data-title="{{ $member->full_name }}">Delete</a>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $crew->links() }}
        @else
            <div class="alert alert-info">@lang('crew.no_crew')</div>
        @endif
    </div>
@endsection

@section('footer_scripts')
    @parent
    <script src="{{ asset('assets/js/frontend/spectrum.js') }}" type="text/javascript"></script>
    <script>
        $(function () {
            $(".color-input").each(function(i, el) {
                var input = $(el);
                input.spectrum({
                    showPaletteOnly: true,
                    showPalette:true,
                    allowEmpty:true,
                    hideAfterPaletteSelect:true,
                    preferredFormat: "hex6",
                    palette: ["{!! implode('","', array_keys(\App\Helpers\Crew::colors())) !!}"],
                    change: function(color) {
                        var val = color.toHexString();
                        if (val === '#000000') {
                            val = 'transparent';
                        }
                        input.closest('td').loading({
                            message: 'Saving...'
                        });
                        $.ajax({
                            url: "{{ route('account.boat.crew.color.update') }}",
                            method: 'PUT',
                            data: {
                                user_id: input.data('id'),
                                color: val,
                                '_token': '{{ csrf_token() }}'
                            },
                            dataType: 'json'
                        }).done(function() {
                            input.closest('tr').find('td:first').css('border-left-color', val);
                        }).always(function() {
                            input.closest('td').loading('stop');
                        });
                    }
                });
            });
        });
    </script>
@endsection
