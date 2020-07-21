{{ Form::open(['route' => 'account.tenders.store', 'id' => 'tender-form', 'method' => 'post', 'files' => true]) }}
@include('tenders.fields')
<div class="actions form-group">
    {{ Form::submit(trans('button.submit'), ['class' => 'btn btn--orange']) }}
</div>
{{ Form::close() }}