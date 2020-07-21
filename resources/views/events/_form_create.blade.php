<h2>@lang('events.new_event')</h2>
{!! Form::open(['route' => 'account.events.store', 'files' => true]) !!}
@include('events.fields')
<div class="actions">
    {!! Form::button('Save', ['type' => 'submit', 'class'=> 'btn btn--orange']); !!}
</div>
{!! Form::close() !!}