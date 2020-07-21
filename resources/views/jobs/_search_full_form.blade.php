@section('header_styles')
    @parent
@stop

<div class="search-form search-form-section form-style">
    <h5>Search for a job</h5>
    {!! Form::open(['route' => 'jobs', 'id' => 'search-jobs-form', 'method' => 'GET']) !!}
    <div class="d-flex justify-content-between align-items-end">
        <div class="col-md-2 group-input {{ $errors->first('group', 'has-error') }}">
            <div class="form-group">
                <label for="group">What job you looking for:</label>
                {{ Form::select('group', $groups, request('group'), ['id' => 'group', 'class' => 'form-control', 'placeholder' => 'Select an option']) }}
                {!! $errors->first('group', '<span class="help-block">:message</span>') !!}
            </div>
        </div>
        <div class="col-md-4 business-categories-input {{ '' != request('group') ? '' : 'd-none' }} {{ $errors->first('business-categories', 'has-error') }}" data-source="{{ route('services.group') }}">
            <div class="form-group">
                <label for="business-categories">Speciality / service:</label>
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
        <div class="col-md-4 location-input {{ $errors->first('location', 'has-error') }}">
            <div class="form-group">
                <label for="location">Location:</label>
                {{ Form::text('location', request('location'), ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Optional']) }}
                {!! $errors->first('location', '<span class="help-block">:message</span>') !!}
            </div>
            <div class="results d-none">
                <ul class="results-list"></ul>
            </div>
        </div>
        <div class="col-md-2 p-0">
            <div class="form-group">
                {!! Form::button('Search job', ['type' => 'submit', 'class'=> 'btn btn-primary btn--orange btn-block', 'disabled' => 'disabled']); !!}
            </div>
        </div>
    </div>
    {!! Form::close() !!}
</div>

@section('footer_scripts')
    @parent
    <script src="{{ asset('assets/js/frontend/search-by-business-categories.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/js/frontend/search-by-location.js') }}" type="text/javascript"></script>
    <script>
        $(function () {
            $('#search-jobs-form').each(function(i, form) {
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

                form.find('button[type=submit]').prop('disabled', false);
            });
        });
    </script>
@stop
