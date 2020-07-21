{!! Form::model($event, ['route' => ['admin.events.update', $event->id], 'method' => 'patch']) !!}
    @include('flash::message')

    @include('admin.events.fields')
{!! Form::close() !!}