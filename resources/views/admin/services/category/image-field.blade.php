<div class="form-group {{ $errors->first('image', 'has-error') }}">
    <div class="col-sm-5">
        {!! Form::label('image', 'Image', ['for' => 'category-image']) !!}
        {!! Form::file('image', ['class' => 'form-control', 'id' => 'category-image']) !!}
        {!! $errors->first('image', '<span class="help-block">:message</span>') !!}
        @if(isset($serviceCategory) && $serviceCategory->hasImage())
            <hr>
            <div class="category-image">
                <img src="{{ $serviceCategory->getThumb('120x120') }}" class="img-thumbnail" alt="{{ $serviceCategory->title }}">
            </div>
        @endif
    </div>
</div>