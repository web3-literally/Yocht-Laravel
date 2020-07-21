@section('header_styles')
    @parent
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/frontend/components/theme.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/frontend/components/datepicker.css') }}">
    <link href="{{ asset('assets/vendors/select2/css/select2.min.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('assets/vendors/select2/css/select2-bootstrap.css') }}" rel="stylesheet">
@endsection

<div class="form-row">
    <div class="col-md-4">
        <div class="form-group {{ $errors->first('title', 'has-error') }}">
            {!! Form::label('title', 'Title *', ['for' => 'title']) !!}
            {!! Form::text('title', null, ['class' => 'form-control', 'autocomplete' => 'off', 'id' => 'title']) !!}
            {!! $errors->first('title', '<span class="help-block">:message</span>') !!}
        </div>
        <div class="form-group {{ $errors->first('set_as', 'has-error') }} w-50">
            {!! Form::label('set_as', 'Set as *', ['for' => 'set_as']) !!}
            {!! Form::select('set_as', $setAsList, null, ['class' => 'form-control', 'placeholder' => '', 'id' => 'set_as']) !!}
            {!! $errors->first('set_as', '<span class="help-block">:message</span>') !!}
        </div>
    </div>
    <div class="col-md-4">
        @php
            if ($id = old('assigned_to_id') ?? (isset($model) ? $model->assigned_to_id : null)) {
                $member = \App\User::getModel()->findOrFail($id);
                $selectedMember = [$member->id => "{$member->member_title} ({$member->account_type_title})"];
            }
        @endphp
        <div class="form-group {{ $errors->first('assigned_to_id', 'has-error') }} w-75">
            {!! Form::label('assigned_to_id', 'Assigned to', ['for' => 'assigned_to_id']) !!}
            {!! Form::select('assigned_to_id', $selectedMember ?? [], null, ['class' => 'form-control', 'id' => 'assigned_to_id']) !!}
            {!! $errors->first('assigned_to_id', '<span class="help-block">:message</span>') !!}
        </div>
    </div>
    <div class="col-md-4">
        @php
            if ($date = old('due_date_at') ?? (isset($model) ? $model->due_date_at : null)) {
                $dateFormated = \Carbon\Carbon::parse($date)->format('m/d/Y');
            }
        @endphp
        <div class="form-group {{ $errors->first('due_date_at', 'has-error') }} w-50">
            {!! Form::label('due_date_at', 'Due date', ['for' => 'due_date_at']) !!}
            <div class="d-flex ">
                {!! Form::text('due_date_at_alt', $dateFormated ?? null, ['class' => 'form-control media-body', 'autocomplete' => 'off', 'id' => 'due_date_at', 'readonly' => 'readonly']) !!}
                <button type="button" class="btn btn--orange pl-2 pr-2">Clear</button>
            </div>
            {!! Form::hidden('due_date_at', null, ['id' => 'due_date_at_alt']) !!}
            {!! $errors->first('due_date_at', '<span class="help-block">:message</span>') !!}
        </div>
        <div class="form-group {{ $errors->first('priority', 'has-error') }} w-25">
            {!! Form::label('priority', 'Priority', ['for' => 'priority']) !!}
            {!! Form::select('priority', $priorityList, old('priority') ?? (isset($model) ? $model->priority : 'medium'), ['class' => 'form-control', 'id' => 'priority']) !!}
            {!! $errors->first('priority', '<span class="help-block">:message</span>') !!}
        </div>
    </div>
</div>
<div class="form-row">
    @foreach($attributes as $attribute)
        <div class="col-md-3">
            <div class="form-group {{ $errors->first($attribute->attribute_code, 'has-error') }}">
                {!! Form::label($attribute->attribute_code, $attribute->frontend_label, ['for' => $attribute->attribute_code]) !!}
                @if ($attribute->frontend_type == 'text')
                    {!! Form::text($attribute->attribute_code, null, ['class' => 'form-control', 'autocomplete' => 'off', 'id' => $attribute->attribute_code]) !!}
                @endif
                @if ($attribute->frontend_type == 'select')
                    {!! Form::select($attribute->attribute_code, $attribute->options(), null, ['class' => 'form-control', 'placeholder' => '', 'id' => $attribute->attribute_code]) !!}
                @endif
                {!! $errors->first($attribute->attribute_code, '<span class="help-block">:message</span>') !!}
            </div>
        </div>
    @endforeach
</div>
<div class="form-row">
    <div class="col-md-12">
        <div class="form-group {{ $errors->first('description', 'has-error') }}">
            {!! Form::label('description', 'Notes', ['for' => 'description']) !!}
            {!! Form::textarea('description', null, ['class' => 'form-control', 'id' => 'description']) !!}
            {!! $errors->first('description', '<span class="help-block">:message</span>') !!}
        </div>
    </div>
</div>

@section('footer_scripts')
    @parent
    <script type="text/javascript" src="{{ asset('assets/vendors/ckeditor/js/ckeditor.js') }}"></script>
    <script src="{{ asset('assets/vendors/select2/js/select2.js') }}" type="text/javascript"></script>
    <script>
        CKEDITOR.replace('description', {
            removeButtons: 'Link,Unlink,Anchor,Image,Table,Format,Indent,Outdent'
        });

        var dueDatePicker = $('#due_date_at');
        dueDatePicker.datepicker({
            altField: "#due_date_at_alt",
            altFormat: "yy-mm-dd"
        });
        dueDatePicker.next('.btn').on('click', function () {
            dueDatePicker.datepicker('setDate', null);
        });

        $("#assigned_to_id").select2({
            ajax: {
                url: "{{ route('account.tasks.members.data') }}",
                dataType: 'json',
                data: function (params) {
                    var query = {
                        search: params.term
                    };
                    return query;
                }
            },
            minimumInputLength: 1,
            templateResult: function (item) {
                if (item.id === '' || item.loading) {
                    return $('<span>' + item.text + '</span>');
                }
                var el = $(
                    '<span class="select-member-item"><img src="'+item.thumb+'"><span class="name">' + item.text + '</span></span>'
                );
                return el;
            },
            placeholder: "",
            theme: "bootstrap",
            width: '100%',
            allowClear: true
        });
    </script>
@endsection