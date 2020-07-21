@php($owners = old('owners') ?? $model->owners ?? [])
<div class="owners-field data-field">
    <div class="template" style="display:none">
        @component('vessels._owners.field-row')
        @endcomponent
    </div>
    <div class="data-rows">
        @foreach($owners as $index => $owner)
            @component('vessels._owners.field-row')
                @slot('index')
                    {{ $index }}
                @endslot
                @slot('email')
                    {{ $owner['email'] }}
                @endslot
                @slot('first_name')
                    {{ $owner['first_name'] }}
                @endslot
                @slot('last_name')
                    {{ $owner['last_name'] }}
                @endslot
                @slot('phone')
                    {{ $owner['phone'] }}
                @endslot
                @slot('phone_home')
                    {{ $owner['phone_home'] }}
                @endslot
            @endcomponent
        @endforeach
    </div>
    <div class="actions mt-0 text-center">
        <button type="button" class="btn data-row-add btn--orange">@lang('button.add')</button>
    </div>
</div>

@section('footer_scripts')
    @parent
    <script>
        $(function() {
            $('.owners-field.data-field').each(function(i, el) {
                var field = $(el);
                field.on('click', '.actions .data-row-add', function() {
                    var row = field.find('.template .data-row').clone();
                    var index = 0;
                    if (field.find('.data-rows > *').length) {
                        index = field.find('.data-rows > *').last().data('index');
                    }
                    index++;
                    row.find('input').each(function(i, el) {
                        $(el).attr('name', function() {
                            return $(this).data('name').replace('[*]', '[' + index + ']');
                        })
                    });
                    row.data('index', index);
                    field.find('.data-rows').append(row);
                });
                field.on('click', '.data-row-delete', function(e) {
                    e.stopPropagation();
                    $(this).closest('.data-row').remove();
                });
            });
        });
    </script>
@stop