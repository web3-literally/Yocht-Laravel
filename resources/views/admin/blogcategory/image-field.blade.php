<div class="form-group {{ $errors->first('image', 'has-error') }}">
    <div class="col-sm-5">
        {!! Form::label('image', 'Image', ['for' => 'blog-category-image']) !!}
        {!! Form::file('image', ['class' => 'form-control', 'id' => 'blog-category-image']) !!}
        {!! $errors->first('image', '<span class="help-block">:message</span>') !!}
        @if(isset($blogcategory) && $blogcategory->hasImage())
            <hr>
            <div class="blog-category-image">
                <img src="{{ $blogcategory->getThumb('120x120') }}" class="img-thumbnail" alt="{{ $blogcategory->title }}">
            </div>
        @endif
    </div>
</div>