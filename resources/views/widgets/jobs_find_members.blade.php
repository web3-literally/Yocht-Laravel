@section('header_styles')
    @parent
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/select2/css/select2.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/select2/css/select2-bootstrap.css') }}">
@stop

<div id="find-members-widget" class="search-form">
    @if($formTitle)
        <h4>{{ $formTitle }}</h4>
    @endif
    {{ Form::open(['url' => $action, 'id' => $formId, 'method' => 'GET']) }}
    <div class="d-flex justify-content-between">
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
                {{ Form::text(null, $businessCategoriesTitle, ['id' => 'business-categories-title', 'class' => 'form-control business-categories-title', 'readonly' => 'readonly', 'placeholder' => '']) }}
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
                        <button type="button" class="btn btn-back btn--orange"><span class="label-back">Back</span>
                        </button>
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
        <div class="col-md-3 location-input {{ 'businesses' == request('group') ? '' : 'd-none' }} {{ $errors->first('location', 'has-error') }}">
            <div class="form-group">
                <label for="location">Search by location:</label>
                {{ Form::text('location', request('location'), ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Optional']) }}
                {!! $errors->first('location', '<span class="help-block">:message</span>') !!}
            </div>
            <div class="results d-none">
                <ul class="results-list"></ul>
            </div>
        </div>
        <div class="col-md-2 keywords-input {{ '' != request('group') ? '' : 'd-none' }} {{ $errors->first('keywords', 'has-error') }}">
            <div class="form-group">
                <label for="keywords">Search by Keywords:</label>
                {{ Form::text('keywords', request('keywords'), ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Optional']) }}
                {!! $errors->first('keywords', '<span class="help-block">:message</span>') !!}
            </div>
        </div>
        <div class="col-md-3 search-input {{ '' == request('group') ? '' : 'd-none' }} {{ $errors->first('search', 'has-error') }}">
            <div class="form-group">
                <label for="search">Search by ID or Name:</label>
                {{ Form::text('search', request('search'), ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Optional']) }}
                {!! $errors->first('search', '<span class="help-block">:message</span>') !!}
            </div>
        </div>
        <div class="col-md-1">
            {{ Form::submit($searchBtnLabel, ['class' => 'btn btn-block btn--orange pl-3 pr-3', 'disabled' => 'disabled']) }}
        </div>
    </div>
    {{ Form::close() }}
</div>

@section('footer_scripts')
    @parent
    <script src="{{ asset('assets/js/frontend/search-by-business-categories.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/js/frontend/search-by-location.js') }}" type="text/javascript"></script>
    <script type="text/javascript" src="{{ asset('assets/vendors/select2/js/select2.js') }}"></script>
    <script>
        $(function () {
            $('#find-members').each(function (i, form) {
                form = $(form);

                form.find('.group-input select').on('change', function () {
                    var val = $(this).val();

                    if (val === '') {
                        form.find('.search-input .form-group input').prop('disabled', false);
                        form.find('.search-input').removeClass('d-none');
                    } else {
                        form.find('.search-input').addClass('d-none');
                        form.find('.search-input .form-group input').prop('disabled', true);
                    }

                    if (val !== '') {
                        form.find('.business-categories-input .form-group input').prop('disabled', false);
                        form.find('.business-categories-input').removeClass('d-none');
                        form.find('.location-input .form-group input').prop('disabled', false);
                        form.find('.location-input').removeClass('d-none');
                        form.find('.keywords-input .form-group input').prop('disabled', false);
                        form.find('.keywords-input').removeClass('d-none');
                    } else {
                        form.find('.business-categories-input').addClass('d-none');
                        form.find('.business-categories-input .form-group input').prop('disabled', true);
                        form.find('.location-input').addClass('d-none');
                        form.find('.location-input .form-group input').prop('disabled', true);
                        form.find('.keywords-input').addClass('d-none');
                        form.find('.keywords-input .form-group input').prop('disabled', true);
                    }
                });

                form.find('.group-input select').change();

                form.find('input[type=submit]').prop('disabled', false);
            });
        });
    </script>
@stop
