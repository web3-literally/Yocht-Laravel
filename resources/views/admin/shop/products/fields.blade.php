<div class="container-fluid">
    <div class="row">
        <!-- Name Field -->
        <div class="col-sm-4">
            <div class="form-group">
                {!! Form::label('name', 'Name *') !!}
                {!! Form::text('name', null, ['class' => 'form-control', 'autocomplete' => 'off']) !!}
            </div>
        </div>

        <div class="col-sm-3">
            <!-- Sku Field -->
            <div class="form-group">
                {!! Form::label('sku', 'Sku') !!}
                {!! Form::text('sku', null, ['class' => 'form-control', 'autocomplete' => 'off']) !!}
            </div>
        </div>
    </div>
    <div class="row">
        <!-- Description Field -->
        <div class="form-group col-sm-7">
            {!! Form::label('description', 'Description') !!}
            {!! Form::textarea('description', null, ['class' => 'form-control']) !!}
        </div>
    </div>
    {{--<div class="row">
        <!-- Stock Field -->
        <div class="form-group col-sm-1">
            {!! Form::label('stock', 'Stock') !!}
            {!! Form::number('stock', null, ['class' => 'form-control', 'autocomplete' => 'off']) !!}
        </div>
    </div>--}}
    <div class="row">
        <!-- Price Field -->
        <div class="form-group col-sm-1">
            {!! Form::label('price', 'Price *') !!}
            {!! Form::text('price', null, ['class' => 'form-control', 'autocomplete' => 'off']) !!}
        </div>
    </div>
    <div class="row">
        <!-- Tax Field -->
        <div class="form-group col-sm-1">
            {!! Form::label('tax', 'Tax') !!}
            {!! Form::text('tax', null, ['class' => 'form-control', 'autocomplete' => 'off']) !!}
        </div>
    </div>
    <div class="row">
        <!-- Url Key Field -->
        <div class="form-group col-sm-2">
            {!! Form::label('url_key', 'Url Key') !!}
            {!! Form::text('url_key', null, ['class' => 'form-control', 'autocomplete' => 'off']) !!}
        </div>
    </div>
    <div class="row">
        <!-- Submit Field -->
        <div class="form-group col-sm-3 text-left">
            {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
            <a href="{!! route('admin.shop.products.index') !!}" class="btn btn-default">Cancel</a>
        </div>
    </div>
</div>