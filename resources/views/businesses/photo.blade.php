@extends('layouts.dashboard-member')

@section('page_class')
    edit-business-photo edit-business businesses @parent
@stop

@section('dashboard-content')
    <div class="dashboard-form-view">
        <h2>@lang('businesses.business_profile')</h2>
        @include('businesses._profile-nav')
        {{ Form::model($business, ['url' => route('account.businesses.profile.photo.update', $business->id), 'id' => 'business-form', 'method' => 'post', 'files' => true]) }}
        <div class="container">
            <div class="row">
                <div class="col-md-12 content business-content mt-4 mb-4">
                    <div class="row">
                        <div class="col-md-12">
                            <h3>@lang('general.account_photo')</h3>
                        </div>
                    </div>
                    <div id="business-images" class="row">
                        <div class="col-md-12">
                            <div class="form-group {{ $errors->first('images.*', 'has-error') }}">
                                @if(isset($business))
                                    <ul class="gallery mt-2 list-unstyled sortable" data-entityname="business_images">
                                        @forelse ($business->images as $image)
                                            <li class="d-inline-block mr-2" data-id="{{ $business->id }}" data-itemId="{{ $image->id }}">
                                                <div class="sortable-item">
                                                    <img src="{{ $image->file->getThumb('120x120') }}" alt="{{ $image->file->filename }}">
                                                    <div class="sortable-handle"><i class="fa fa-sort"></i></div>
                                                    <a class="remove-handle" href="#" data-url="{{ route('account.businesses.profile.photo.images.delete', ['image_id' => $image->id, 'id' => $business->id]) }}"><i class="fas fa-trash-alt"></i></a>
                                                </div>
                                            </li>
                                        @empty
                                            <p>No images</p>
                                        @endforelse
                                    </ul>
                                @endif
                                <div class="increment mt-2 mb-2">
                                    <button class="btn btn-success" type="button"><i class="glyphicon glyphicon-plus"></i> Add</button>
                                </div>
                                {!! $errors->first('images.*', '<span class="help-block">:message</span>') !!}
                            </div>
                            <div class="clone d-none">
                                <div class="control-group input-group mb-1">
                                    <input type="file" name="images[]" class="form-control">
                                    <div class="input-group-append">
                                        <button class="btn btn-danger" type="button">Remove</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    {{ Form::submit(trans('button.save'), ['class' => 'btn btn--orange']) }}
                </div>
            </div>
        </div>
        {{ Form::close() }}
    </div>
@endsection

@section('footer_scripts')
    @parent
    <script>
        $(document).ready(function () {
            var block = $('#business-images');
            $(".btn-success", block).click(function () {
                var input = $(".clone > *:first", block).clone();
                $(".increment", block).after(input);
            });
            block.on("click", ".btn-danger", function () {
                $(this).parents(".control-group").remove();
            });
        });

        $(document).ready(function () {
            var changePosition = function (requestData) {
                requestData['_token'] = $('form input[name=_token]').val();
                $.ajax({
                    'url': '{{ route('dashboard.sort') }}',
                    'type': 'POST',
                    'data': requestData,
                    'success': function (data) {
                        if (data.success) {
                            console.log('Saved!');
                        } else {
                            console.error(data.errors);
                        }
                    }
                });
            };

            $(document).ready(function () {
                var $sortableTable = $('.sortable');
                if ($sortableTable.length > 0) {
                    $sortableTable.sortable({
                        containment: "parent",
                        handle: '.sortable-handle',
                        axis: 'x',
                        update: function (a, b) {
                            var entityName = $(this).data('entityname');
                            var $sorted = b.item;

                            var $previous = $sorted.prev();
                            var $next = $sorted.next();

                            if ($previous.length > 0) {
                                changePosition({
                                    parentId: $sorted.data('parentid'),
                                    type: 'moveAfter',
                                    entityName: entityName,
                                    id: $sorted.data('itemid'),
                                    positionEntityId: $previous.data('itemid')
                                });
                            } else if ($next.length > 0) {
                                changePosition({
                                    parentId: $sorted.data('parentid'),
                                    type: 'moveBefore',
                                    entityName: entityName,
                                    id: $sorted.data('itemid'),
                                    positionEntityId: $next.data('itemid')
                                });
                            }
                        },
                        cursor: "move"
                    });
                }
            });

            $(function () {
                $('.gallery').on('click', '.remove-handle', function () {
                    var self = $(this);
                    var item = self.closest('li');
                    if (confirm('Are you sure?')) {
                        item.loading({
                            message: ''
                        });
                        $.ajax({
                            'url': self.data('url'),
                            'type': 'GET',
                            'dataType': 'json',
                            'success': function (data) {
                                item.loading('stop');
                                if (data.success) {
                                    item.remove();
                                }
                            },
                            complete: function () {
                                item.loading('stop');
                            }
                        });
                    }
                    return false;
                });
            });
        });
    </script>
@endsection