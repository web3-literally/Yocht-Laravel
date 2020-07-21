@php($staff = old('staff') ?? $business->staff ?? [])
<div class="staff-field data-field">
    <div class="template d-none">
        @component('businesses._staff.field-row')
        @endcomponent
    </div>
    <div class="data-rows">
        @foreach($staff as $index => $item)
            @component('businesses._staff.field-row')
                @slot('index')
                    {{ $index }}
                @endslot
                @slot('name')
                    {{ $item['name'] }}
                @endslot
                @slot('phone')
                    {{ $item['phone'] ?? '' }}
                @endslot
                @slot('email')
                    {{ $item['email'] ?? '' }}
                @endslot
                @slot('type')
                    {{ $item['type'] }}
                @endslot
            @endcomponent
        @endforeach
    </div>
    <div class="actions mt-0">
        <div class="dropdown">
            <button class="btn btn-secondary dropdown-toggle btn--orange" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                @lang('button.add')
            </button>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <button type="button" class="dropdown-item data-row-add" data-type="manager">Manager</button>
                <button type="button" class="dropdown-item data-row-add" data-type="salesman">Salesman</button>
            </div>
        </div>
    </div>
</div>

@section('footer_scripts')
    @parent
    <script>
        $(function() {
            $('.staff-field.data-field').each(function(i, el) {
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
                    row.find('.data-name').attr('placeholder', row.find('.data-name').attr('placeholder').replace('[user-type]', $(this).text()));
                    row.find('.data-type').val($(this).data('type'));
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