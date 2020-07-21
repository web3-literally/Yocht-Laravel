@section('header_styles')
    @parent
    <link href="{{ asset('assets/vendors/select2/css/select2.min.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('assets/vendors/select2/css/select2-bootstrap.css') }}" rel="stylesheet">
@stop

{{ Form::open(['url' => url()->current(), 'id' => 'search-classified-category-listings', 'method' => 'GET']) }}
<div class="col-12"><h5>@lang('classifieds.search_classified_listings')</h5></div>
<div class="d-flex justify-content-between align-items-end">
    <div class="col-4">
        <div class="form-group">
            <label for="category_id">Category:</label>
            {{ Form::select('category_id', $categories, $category, ['class' => 'form-control', 'placeholder' => 'Select an category', 'id' => 'classified-category-id']) }}
        </div>
    </div>
    <div class="col-4">
        <div class="form-group">
            <label for="location">Location:</label>
            {{ Form::text('location', $location, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Optional', 'id' => 'classified-location']) }}
            {!! $errors->first('location', '<span class="help-block">:message</span>') !!}
        </div>
    </div>
    <div class="col4">
        {{ Form::submit('Search Now', ['class' => 'btn btn-block btn--orange']) }}
    </div>
</div>
{{ Form::close() }}

@section('footer_scripts')
    @parent
    <script src="{{ asset('assets/vendors/select2/js/select2.js') }}" type="text/javascript"></script>
    <script>
        $("#classified-category-id").select2({
            theme: "bootstrap",
            allowClear: false
        });
    </script>
@stop