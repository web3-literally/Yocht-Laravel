{!! Form::model($item, ['route' => ['admin.menus.item.update', $item->id], 'method' => 'patch']) !!}

@include('flash::message')

@include('admin.menus.item.item_fields')

{!! Form::hidden('id', $item->id) !!}
{!! Form::hidden('menu_id', $item->menu_id) !!}

{!! Form::close() !!}