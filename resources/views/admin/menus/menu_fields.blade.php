{{-- page level styles --}}
@section('header_styles')
    <link href="{{ asset('assets/vendors/bootstrap-tagsinput/css/bootstrap-tagsinput.css') }}" rel="stylesheet" />
@stop

<div class="container-fluid">
    <div class="row">
        <div class="col-sm-9">
            <div class="form-group w-50 {{ $errors->first('label', 'has-error') }}">
                {!! Form::text('label', null, array('class' => 'form-control input-lg', 'autocomplete'=>'off', 'placeholder'=> 'Label')) !!}
                <span class="help-block">{{ $errors->first('label', ':message') }}</span>
            </div>
            <div class="form-group w-25 {{ $errors->first('type', 'has-error') }}">
                {!! Form::select('type', $types, current($types), array('class' => 'form-control select2', 'id'=>'type')) !!}
                <span class="help-block">{{ $errors->first('type', ':message') }}</span>
            </div>
            <div class="form-group w-50">
                {!! Form::text('html_class', null, array('class' => 'form-control input-lg', 'autocomplete'=>'off', 'data-role'=>"tagsinput", 'placeholder'=>'CSS Class')) !!}
            </div>
        </div>
        <!-- /.col-sm-9 -->
        <div class="col-sm-3">
            <div class="form-group action-buttons">
                <button type="submit" class="btn btn-primary">Save</button>
                <a href="{!! route('admin.menus.index') !!}" class="btn btn-danger">Cancel</a>
            </div>
            <hr>
        </div>
        <!-- /.col-sm-3 -->
    </div>
</div>

{{-- page level scripts --}}
@section('footer_scripts')
    <script src="{{ asset('assets/vendors/bootstrap-tagsinput/js/bootstrap-tagsinput.js') }}" type="text/javascript" ></script>
@stop