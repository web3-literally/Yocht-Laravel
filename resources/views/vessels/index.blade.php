@extends('layouts.dashboard-member')

@section('page_class')
    dashboard-vessels @parent
@stop

@section('header_styles')
    @parent
    <link href="{{ asset('assets/css/frontend/spectrum.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('assets/css/frontend/flag-icon.css') }}" rel="stylesheet" />
@endsection

@section('dashboard-content')
    <div class="dashboard-table-view">
        <div class="dashboard-btn-group-top dashboard-btn-group">
            <h2>@lang('vessels.vessels') {{--<small>({{ $vesselCount }}/{{ $vesselSlotsCount }} vessels, {{ $tenderCount }}/{{ $tenderSlotsCount }} tenders)</small>--}}</h2>
            <a class="btn btn--orange" href="{{ route('account.tenders.add') }}" role="button">Add a New Tender</a>
            <a class="btn btn--orange" href="{{ route('account.vessels.add') }}" role="button">Add a New Vessel</a>
        </div>
        @if($vessels->count())
            <table class="dashboard-table table">
                <thead>
                <tr>
                    <th scope="col" colspan="2"></th>
                    <th class="no-wrap text-center" scope="col" width="1">Actions</th>
                </tr>
                </thead>
                <tbody>
                    @foreach($vessels as $vessel)
                        <tr class="boat">
                            <td width="10" style="border-left-width: 7px; border-left-style: solid; border-left-color: {{ $vessel->listing_color }};"></td>
                            <td>
                                <div class="container-fluid">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <img src="{{ $vessel->getThumb('184x114') }}" alt="{{ $vessel->title }}">
                                        </div>
                                        <div class="col-md-5">
                                            <h3 class="mb-2">
                                                <a href="{{ route('account.dashboard', ['related_member_id' => $vessel->user_id]) }}">{{ $vessel->title }}</a>
                                                @if ($vessel->is_primary)
                                                    <small class="label badge-info">Primary</small>
                                                @endif
                                            </h3>
                                            <span class="category label">{{ $vessel->type }}</span><br>
                                            <p class="mt-2 mb-0">
                                                @if($vessel->flag)<span class="flag-icon flag-icon-{{ strtolower($vessel->flag) }}"></span>@endif <span>{{ $vessel->registered_port_city ?? '' }}</span>
                                            </p>
                                        </div>
                                        @if($vessel->type == 'vessel')
                                            <div class="offset-4 col-md-1">
                                                @if(Sentinel::getUser()->hasAccess('vessels.colors'))
                                                    {{ Form::hidden('color', $vessel->listing_color, ['class' => 'color-input', 'data-id' => $vessel->id]) }}
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="actions no-wrap">
                                <a href="{{ route('account.dashboard', ['related_member_id' => $vessel->user_id]) }}" class="btn">{{ $vessel->isVessel() ? trans('general.vessel_dashboard')  : trans('general.tender_dashboard') }}</a>
                                <br>
                                <a href="{{ $vessel->getPublicProfileLink() }}" class="btn">@lang('general.manage_profile')</a>
                                @if ($vessel->type == 'vessel')
                                    <a href="{{ $vessel->user->getPublicProfileLink() }}" class="btn" target="_blank">@lang('general.public_profile')</a>
                                @endif
                                @if(Sentinel::getUser()->isMemberOwnerAccount())
                                    <br>
                                    @if ($vessel->type == 'vessel')
                                        <a href="{{ route('account.tenders.add', ['parent_id' => $vessel->id]) }}" class="btn">@lang('vessels.add_tender')</a>
                                    @endif
                                    <a href="{{ route('account.boat.transfer.step', ['boat_id' => $vessel->id, 'step' => 1]) }}" class="btn">@lang('vessels.transfer.transfer')</a>
                                    <a href="{{ route('account.'.$vessel->type.'s.remove', $vessel->id) }}" onclick="return confirm('Are you sure to delete the &quot;'+ $(this).data('title') +'&quot; vessel?')" class="btn" data-title="{{ $vessel->title }}">Delete</a>
                                @endif
                            </td>
                        </tr>
                        @if($vessel->tenders->count())
                            @foreach($vessel->tenders as $sub)
                                <tr class="sub-boat sub-boat-{{ $vessel->id }}">
                                    <td width="10" style="border-left-width: 7px; border-left-style: solid; border-left-color: {{ $vessel->listing_color }};"></td>
                                    <td>
                                        <div class="container-fluid">
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <img src="{{ $sub->getThumb('184x114') }}" alt="{{ $sub->title }}">
                                                </div>
                                                <div class="col-md-5">
                                                    <h3 class="mb-2">
                                                        <a href="{{ route('account.dashboard', ['related_member_id' => $sub->user_id]) }}">{{ $sub->title }}</a>
                                                    </h3>
                                                    <span class="category label">{{ $sub->type }}</span><br>
                                                    @if($sub->registered_port)
                                                        <p class="mt-2 mb-0">
                                                            <span class="flag-icon flag-icon-{{ strtolower($sub->registered_port) }}"></span> <span>{{ $sub->registeredPort->name }}</span>
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="actions no-wrap">
                                        <a href="{{ route('account.dashboard', ['related_member_id' => $sub->user_id]) }}" class="btn">{{ $sub->isVessel() ? trans('general.vessel_dashboard')  : trans('general.tender_dashboard') }}</a>
                                        <br>
                                        <a href="{{ $sub->getPublicProfileLink() }}" class="btn">@lang('general.manage_profile')</a>
                                        @if(Sentinel::getUser()->isMemberOwnerAccount())
                                            <br>
                                            <a href="{{ route('account.boat.transfer.step', ['boat_id' => $sub->id, 'step' => 1]) }}" class="btn">@lang('vessels.transfer.transfer')</a>
                                            <a href="{{ route('account.'.$sub->type.'s.remove', $sub->id) }}" onclick="return confirm('Are you sure to delete the &quot;'+ $(this).data('title') +'&quot; vessel?')" class="btn" data-title="{{ $sub->title }}">Delete</a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    @endforeach
                </tbody>
            </table>
            {{ $vessels->links() }}
        @else
            <div class="alert alert-info">@lang('vessels.no_vessels')</div>
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
                    palette: ["{!! implode('","', array_keys(\App\Helpers\Vessel::colors())) !!}"],
                    change: function(color) {
                        var val = color.toHexString();
                        if (val === '#000000') {
                            val = 'transparent';
                        }
                        input.closest('td').loading({
                            message: 'Saving...'
                        });
                        $.ajax({
                            url: "{{ route('account.vessels.color.update') }}",
                            method: 'PUT',
                            data: {
                                vessel_id: input.data('id'),
                                color: val,
                                '_token': '{{ csrf_token() }}'
                            },
                            dataType: 'json'
                        }).done(function() {
                            input.closest('tr').find('td:first').css('border-left-color', val);
                            input.closest('table').find('.sub-boat-'+input.data('id')).each(function() {
                                $(this).children('td:first').css('border-left-color', val);
                            });
                        }).always(function() {
                            input.closest('td').loading('stop');
                        });
                    }
                });
            });
        });
    </script>
@endsection
