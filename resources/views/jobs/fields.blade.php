@section('header_styles')
    @parent
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/frontend/components/theme.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/frontend/components/datepicker.css') }}">
    <link href="{{ asset('assets/vendors/select2/css/select2.min.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('assets/vendors/select2/css/select2-bootstrap.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendors/jstree/css/style.min.css') }}" rel="stylesheet" type="text/css"/>
@endsection

<div class="form-row">
    <input type="hidden" name="visibility" value="{{ $visibility }}">
    <div class="col-md-6">
        <div class="form-row">
            <div class="col-md-6">
                @if(isset($job) && $job->exists)
                    @php
                        $membersIds = [];
                        $job->members->each(function ($item, $key) use (&$membersIds) {
                            $membersIds[] = $item->member_id;
                        });
                    @endphp
                @else
                    @php($membersIds = request('members'))
                @endif
                @if($membersIds)
                    @php($members = \App\User::whereIn('id', $membersIds)->get())
                    <div class="form-group {{ $errors->first('members', 'has-error') }} {{ $errors->first('members.*', 'has-error') }}">
                        {!! Form::label('members', 'Members') !!}
                        <div class="for-members-list">
                            @foreach($members as $member)
                                <span>{{ $loop->index == 0 ? '' : ', ' }}<strong>{{ $member->member_title }}</strong></span>
                            @endforeach
                        </div>
                        @foreach($members as $member)
                            <input type="hidden" name="members[]" value="{{ $member->id }}">
                        @endforeach
                        {!! $errors->first('members', '<span class="help-block">:message</span>') !!}
                        {!! $errors->first('members.*', '<span class="help-block">:message</span>') !!}
                    </div>
                @endif
            </div>
        </div>
        <div class="form-group {{ $errors->first('title', 'has-error') }}">
            {!! Form::label('title', 'Title *', ['for' => 'job-title']) !!}
            {!! Form::text('title', null, ['class' => 'form-control', 'autocomplete' => 'off', 'id' => 'job-title']) !!}
            {!! $errors->first('title', '<span class="help-block">:message</span>') !!}
        </div>
        {{--<div class="col-md-6">
            <div class="form-group {{ $errors->first('category_id', 'has-error') }}">
                {!! Form::label('category_id', 'Category *', ['for' => 'job-category']) !!}
                {!! Form::select('category_id', ['' => ''] + $categories, request('category_id'), ['class' => 'form-control', 'autocomplete' => 'off', 'id' => 'job-category']) !!}
                {!! $errors->first('category_id', '<span class="help-block">:message</span>') !!}
            </div>
        </div>--}}
        <div class="form-group {{ $errors->first('p_o_number', 'has-error') }} w-50">
            {!! Form::label('p_o_number', 'P/O Number', ['for' => 'job-p-o-number']) !!}
            {!! Form::text('p_o_number', null, ['class' => 'form-control', 'autocomplete' => 'off', 'id' => 'job-p-o-number']) !!}
            {!! $errors->first('p_o_number', '<span class="help-block">:message</span>') !!}
        </div>
        <div class="form-group {{ $errors->first('warranty', 'has-error') }} text-left w-25">
            {!! Form::label('warranty', 'Warranty', ['for' => 'job-warranty']) !!}
            {!! Form::hidden('warranty', 0) !!}
            {!! Form::checkbox('warranty', 1, isset($job) && $job->warranty , ['id' => 'job-warranty']) !!}<label for="job-warranty" class="m-0"></label>
            {!! $errors->first('warranty', '<span class="help-block">:message</span>') !!}
        </div>
        <div class="form-group {{ $errors->first('image', 'has-error') }}">
            {!! Form::label('image', 'Image', ['for' => 'job-image']) !!}
            {!! Form::file('image', ['class' => 'form-control', 'id' => 'job-image']) !!}
            {!! $errors->first('image', '<span class="help-block">:message</span>') !!}
            @if(isset($job) && $job->hasImage())
                <hr>
                <div class="job-image">
                    <img src="{{ $job->getThumb('120x120') }}" class="img-thumbnail" alt="{{ $job->title }}">
                    <a href="{{ route('account.jobs.image.delete', $job->id) }}" class="btn btn-danger">Remove</a>
                </div>
            @endif
        </div>
        {{--<div class="job-without-vessel">
            <div class="form-group {{ $errors->first('address', 'has-error') }}">
                {!! Form::label('address', 'Address *', ['for' => 'job-address']) !!}
                {!! Form::text('address', null, ['class' => 'form-control', 'autocomplete' => 'off', 'id' => 'job-address']) !!}
                {!! $errors->first('address', '<span class="help-block">:message</span>') !!}
            </div>
        </div>--}}
    </div>
    <div class="col-md-6">
        <div class="form-group {{ $errors->first('categories', 'has-error') }}">
            {!! Form::label('categories', 'Business categories', ['for' => 'business-categories']) !!}
            {!! $errors->first('categories', '<span class="help-block">:message</span>') !!}
            {!! $errors->first('categories.*', '<span class="help-block">:message</span>') !!}
            {!! $errors->first('services', '<span class="help-block">:message</span>') !!}
            {!! $errors->first('services.*', '<span class="help-block">:message</span>') !!}
            <div class="form-control">
                @foreach($selectedCategories as $category)
                    <label class="d-block">{{ $category->full_label }}</label>
                    <input type="hidden" name="categories[]" value="{{ $category->id }}">
                @endforeach
            </div>
        </div>
    </div>
</div>
<div class="form-row">
    <div class="col-md-12">
        <div class="form-group {{ $errors->first('content', 'has-error') }}">
            {!! Form::label('content', 'Description *', ['for' => 'job-content']) !!}
            {!! Form::textarea('content', null, ['class' => 'form-control', 'autocomplete' => 'off', 'id' => 'job-content']) !!}
            {!! $errors->first('content', '<span class="help-block">:message</span>') !!}
        </div>
    </div>
</div>

@section('footer_scripts')
    @parent
    <script type="text/javascript" src="{{ asset('assets/vendors/ckeditor/js/ckeditor.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/frontend/components/datepicker.js') }}"></script>
    <script src="{{ asset('assets/vendors/select2/js/select2.js') }}" type="text/javascript"></script>
    <script type="text/javascript" src="{{ asset('assets/js/frontend/jobs.js') }}"></script>
    <script src="{{ asset('assets/vendors/jstree/js/jstree.min.js') }}" type="text/javascript"></script>
    {{--<script id="services-tree-data" type="application/json">@json($servicesTree)</script>--}}
    <script>
        CKEDITOR.replace('job-content', {
            removeButtons: 'Link,Unlink,Anchor,Image,Table,Format,Indent,Outdent'
        });
    </script>
    <script>
        $(function () {
            var ready = false;
            var tree = $("#services-tree");
            var data = JSON.parse(document.getElementById('services-tree-data').innerHTML);
            var selected =  [].concat(@json($selectedServices));

            tree.jstree({
                "core": {
                    'expand_selected_onload' : false,
                    'multiple': true,
                    "animation": 0,
                    "check_callback": true,
                    'data': data
                },
                "types": {
                    "category": {
                        "icon": "fa fa-folder color-orange"
                    },
                    "service": {
                        "icon": "fa fa-box color-orange"
                    }
                },
                "checkbox": {
                    "keep_selected_style": false
                },
                "plugins": ["checkbox", "types", "changed"]
            }).on('loaded.jstree', function () {
                tree.jstree('select_node', selected);
                ready = true;
            }).on("changed.jstree", function (e, data) {
                if (ready && (data.action === 'select_node' || data.action === 'deselect_node')) {
                    var selectedData = tree.jstree('get_selected', true);
                    var ids = [];
                    if (selectedData.length) {
                        for (var i = 0; i < selectedData.length; i++) {
                            if (selectedData[i].type === 'service') {
                                ids.push(selectedData[i].id);
                            }
                        }
                    }
                    $('#services').val(ids.join(','));
                }
            });
        });
    </script>
@endsection