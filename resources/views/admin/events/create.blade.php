{!! Form::open(['route' => ['admin.events.store'], 'method' => 'patch']) !!}
    @include('flash::message')

    @include('admin.events.fields')
{!! Form::close() !!}