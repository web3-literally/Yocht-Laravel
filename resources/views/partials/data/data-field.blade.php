@section('header_styles')
    @parent
    <link href="{{ asset('assets/css/sortable.css') }}" rel="stylesheet" type="text/css">
@stop

<div class="data-field">
    <div class="template" style="display:none">
        @component('partials.data.data-field-row')
        @endcomponent
    </div>
    <div class="data-rows">
        @foreach($data as $key => $value)
            @component('partials.data.data-field-row')
                @slot('key')
                    {{ $key }}
                @endslot
                @slot('value')
                    {{ $value }}
                @endslot
            @endcomponent
        @endforeach
    </div>
    <div class="actions mt-3">
        <button type="button" class="btn btn-primary">Add</button>
    </div>
</div>

@section('footer_scripts')
    @parent
    <script>
        $(function() {
            $('.data-field').each(function(i, el) {
                var field = $(el);
                field.on('click', '.actions .btn-primary', function() {
                    var row = field.find('.template .data-row').clone();
                    field.find('.data-rows').append(row);
                });
                field.on('click', '.data-row-delete', function(e) {
                    e.stopPropagation();
                    $(this).closest('.data-row').remove();
                });
                field.find('.data-rows').sortable({
                    handle: '.sortable-handle',
                    axis: 'y',
                });
            });
        });
    </script>
@stop