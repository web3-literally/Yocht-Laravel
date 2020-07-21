<h2>@lang('events.edit_event')</h2>
    {!! Form::model($event, ['route' => ['account.events.update', $event->id], 'files' => true]) !!}
    @include('events.fields')
    {!! Form::button('Save', ['type' => 'submit', 'class'=> 'btn btn--orange']); !!}
{!! Form::close() !!}