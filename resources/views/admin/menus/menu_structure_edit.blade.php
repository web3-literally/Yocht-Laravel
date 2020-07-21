@extends('admin/layouts/default')

@section('title')
    Menu Structure @parent
@stop

@section('header_styles')
    <link href="{{ asset('assets/vendors/bootstrap-tagsinput/css/bootstrap-tagsinput.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/vendors/jstree/css/style.min.css') }}" rel="stylesheet" type="text/css"/>
@stop

@section('content')
    @include('core-templates::common.errors')
    <section class="content-header">
        <h1>Menu Structure</h1>
        <ol class="breadcrumb">
            <li>
                <a href="{{ route('admin.dashboard') }}">
                    <i class="livicon" data-name="home" data-size="16" data-color="#000"></i>
                    Dashboard
                </a>
            </li>
            <li><a href="{{ route('admin.menus.index') }}">Menus</a></li>
            <li class="active">Edit Menu Structure</li>
        </ol>
    </section>
    <section class="content paddingleft_right15">
        <div class="row">
            <div class="col-sm-12">
                <div class="card panel-primary">
                    <div class="card-heading">
                        <h4 class="card-title">
                            <i class="livicon" data-name="tree" data-size="16" data-loop="true" data-c="#fff" data-hc="white"></i>
                            Edit Menu Structure
                        </h4></div>
                    <div class="card-body">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-md-3">
                                    <div id="tree" data-source="{{ route('admin.menus.get.tree', $menu->id) }}"></div>
                                </div>
                                <div class="col-md-9">
                                    <div id="item-form" style="min-height: 438px;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop

@section('footer_scripts')
    <script src="{{ asset('assets/vendors/ckeditor/js/ckeditor.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/vendors/bootstrap-tagsinput/js/bootstrap-tagsinput.js') }}" type="text/javascript" ></script>
    <script src="{{ asset('assets/vendors/jstree/js/jstree.min.js') }}" type="text/javascript"></script>
    <script>
        $(function () {
            var tree = $('#tree');
            var item = $("#item-form");
            tree.jstree({
                "core": {
                    'multiple': false,
                    "animation": 0,
                    "check_callback": true,
                    "themes": {"stripes": true},
                    'data': {
                        'url': tree.data('source'),
                        'data': function (node) {
                            return {'id': node.id};
                        }
                    }
                },
                'contextmenu' : {
                    'items' : function(node) {
                        var tmp = $.jstree.defaults.contextmenu.items();
                        delete tmp.ccp;
                        return tmp;
                    }
                },
                "types": {
                    "#": {
                        "max_children": 1,
                        "max_depth": 5,
                        "valid_children": ["root"]
                    },
                    "root": {
                        "icon": "fa fa-sitemap"
                    },
                    "default": {
                        "icon": "fa fa-link"
                    },
                    "home": {
                        "icon": "fa fa-home"
                    },
                    "link": {
                        "icon": "fa fa-link"
                    },
                },
                "plugins": [
                    "contextmenu", "dnd", "search",
                    "state", "types", "wholerow"
                ]
            }).on('create_node.jstree', function (e, data) {
                $.post("{{ route('admin.menus.item.store') }}", {
                    'label': data.node.text,
                    'menu_id': '{{ $menu->id }}',
                    'parent': data.node.parent == 'root' ? null : data.node.parent,
                    'type': data.node.type
                }).done(function (d) {
                    data.instance.set_id(data.node, d.id);
                }).fail(function () {
                    data.instance.refresh();
                });
            }).on('rename_node.jstree', function (e, data) {
                $.post("{{ route('admin.menus.item.rename') }}", {
                    'id': data.node.id,
                    'label': data.text
                }).done(function (d) {
                    data.instance.set_id(data.node, d.id);
                }).fail(function () {
                    data.instance.refresh();
                });
            }).on('delete_node.jstree', function (e, data) {
                $.post("{{ route('admin.menus.item.delete') }}", {
                    'id': data.node.id
                }).fail(function () {
                    data.instance.refresh();
                });
            }).on('move_node.jstree', function (e, data) {
                $.post("{{ route('admin.menus.item.move') }}", {
                    'id': data.node.id,
                    'parent': data.node.parent == 'root' ? null : data.node.parent,
                    'order': data.position + 1
                }).done(function (d) {
                    data.instance.load_node(d.parent);
                }).fail(function () {
                    data.instance.refresh();
                });
            }).on('select_node.jstree',  function (e, data) {
                item.loading();
                item.load("{{ route('admin.menus.item.edit') }}?id=" + data.node.id, function() {
                    initCKEditor.call();
                    item.loading('stop');
                });
            });
            item.on('submit', 'form', function(e) {
                e.preventDefault();
                var form = $(this);

                item.loading();
                $.ajax({
                    type: form.attr('method'),
                    url: form.attr('action'),
                    data: form.serialize(),
                    success: function (data) {
                        item.html(data);
                        initCKEditor.call();
                    },
                    complete: function () {
                        item.loading('stop');
                    }
                });

                return false;
            });
            var initCKEditor =function() {
                CKEDITOR.replace('item-content', {
                    extraPlugins: 'autogrow',
                    removePlugins: 'preview,sourcearea,resize',
                    autoGrow_onStartup: true
                });
            };
        });
    </script>
@stop