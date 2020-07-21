@extends('layouts.dashboard-member')

{{-- Page Title --}}
@section('title')
    Favorites Jobs @parent
@stop

{{-- Page CSS Classes --}}
@section('page_class')
    favorites-jobs @parent
@stop

@section('dashboard-title')
    Favorites Jobs
@endsection

@section('dashboard-content')
    <div class="dashboard-table-view">
        <div class="dashboard-btn-group-top dashboard-btn-group">
            <h2>@lang('general.some_favorite', ['some' => trans('jobs.jobs')])</h2>
        </div>

        <div class="search-form form-style">
            {{ Form::open(['url' => url()->current(), 'id' => 'filter-form', 'method' => 'GET']) }}
            <div class="d-flex justify-content-between align-items-end">
                <div class="col-md-2 group-input {{ $errors->first('group', 'has-error') }}">
                    <div class="form-group">
                        <label for="group">What do you need:</label>
                        {{ Form::select('group', $groups, request('group'), ['id' => 'group', 'class' => 'form-control', 'placeholder' => 'Select an option']) }}
                        {!! $errors->first('group', '<span class="help-block">:message</span>') !!}
                    </div>
                </div>
                <div class="col-md-4 business-categories-input {{ 'businesses' == request('group') ? '' : 'd-none' }} {{ $errors->first('business-categories', 'has-error') }}" data-source="{{ route('services.group') }}">
                    <div class="form-group">
                        <label for="business-categories">Specializing in:</label>
                        {{ Form::text(null, $titledCategories, ['id' => 'business-categories-title', 'class' => 'form-control business-categories-title', 'readonly' => 'readonly', 'placeholder' => '']) }}
                        {!! $errors->first('business-categories', '<span class="help-block">:message</span>') !!}
                    </div>
                    <div class="business-categories-selected">
                        @foreach($selectedCategories as $categoryId)
                            <input type="hidden" name="categories[]" value="{{ $categoryId }}">
                        @endforeach
                    </div>
                    <div class="business-services-selected">
                        @foreach($selectedServices as $serviceId)
                            <input type="hidden" name="services[]" value="{{ $serviceId }}">
                        @endforeach
                    </div>
                    <div class="dropdown-container d-none">
                        <div class="wizard">
                            <div class="top-panel">
                                <button type="button" class="btn btn-back btn--orange"><span class="label-back">Back</span></button>
                                <div class="multiselect"><input id="multiselect-checkbox" type="checkbox">
                                    <label for="multiselect-checkbox" class="multiselect-text">Multi select</label>
                                    <button type="button" class="btn btn-continue btn--orange"><span class="label-continue">Continue</span>
                                    </button>
                                </div>
                            </div>
                            <div class="results"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 keywords-input {{ $errors->first('keywords', 'has-error') }}">
                    <div class="form-group">
                        <label for="keywords">Search by Keywords:</label>
                        {{ Form::text('keywords', request('keywords'), ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Optional']) }}
                        {!! $errors->first('keywords', '<span class="help-block">:message</span>') !!}
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        {{ Form::submit(trans('general.search'), ['class' => 'btn btn-block btn--orange', 'disabled' => 'disabled']) }}
                    </div>
                </div>
            </div>
            {{ Form::close() }}
        </div>

        @if ($rows)
            <table id="jobs-listing" class="dashboard-table table">
                <thead>
                <tr>
                    <th scope="col" width="1"></th>
                    <th scope="col"></th>
                    <th scope="col" width="1">Status</th>
                    <th scope="col" width="1">Actions</th>
                </tr>
                </thead>
                <tbody>
                    @foreach($rows as $row)
                        @php($job = $row->job)
                        <tr>
                            <td>
                                <img src="{{ $job->getThumb('120x120') }}" alt="{{ $job->title }}">
                            </td>
                            <td>
                                <h3>{{ $job->title }}</h3>
                                {{--<strong><small>{{ $job->category->label }}</small></strong><br>--}}
                                <small>Starts at: {{ is_null($job->starts_at) ? '-' : $job->starts_at->toFormattedDateString() }}</small>
                                <div>
                                    {!! HtmlTruncator::truncate($job->content, 24) !!}
                                </div>
                            </td>
                            <td>
                                <span class="label label-info">{{ $job->statusLabel }}</span>
                            </td>
                            <td>
                                <a href="{{ route('jobs.show', $job->slug) }}" class="btn mb-3 link--orange" target="_blank">View</a>
                                <button class="btn favorite-add btn--orange" data-url="{{ route('favorites.jobs.store', $job->id) }}" style="display: none;">
                                    Favorite
                                </button>
                                <button class="btn favorite-delete btn--orange" data-url="{{ route('favorites.jobs.delete', $job->id) }}">
                                    UnFavorite
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $rows->links() }}
        @endif
    </div>
@endsection

@section('footer_scripts')
    @parent
    <script src="{{ asset('assets/js/frontend/search-by-business-categories.js') }}" type="text/javascript"></script>
    <script>
        $(function () {
            $('#filter-form').each(function (i, form) {
                form = $(form);

                form.find('.group-input select').on('change', function () {
                    var val = $(this).val();

                    if (val !== '') {
                        form.find('.business-categories-input .form-group input').prop('disabled', false);
                        form.find('.business-categories-input').removeClass('d-none');
                    } else {
                        form.find('.business-categories-input').addClass('d-none');
                        form.find('.business-categories-input .form-group input').prop('disabled', true);
                    }
                });

                form.find('.group-input select').change();

                form.find('input[type=submit]').prop('disabled', false);
            });
        });
    </script>
    <script>
        $(function() {
            $('#jobs-listing tbody tr').each(function(i, el) {
                var buttons = $(el).find('.favorite-add, .favorite-delete');
                buttons.on('click', function() {
                    var clicked = $(this);
                    if (!clicked.hasClass('disabled')) {
                        clicked.addClass('disabled');
                        $.ajax({
                            method: "GET",
                            url: clicked.data('url'),
                            contentType: 'json',
                            success: function () {
                                buttons.toggle();
                            },
                            complete: function() {
                                clicked.removeClass('disabled');
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection