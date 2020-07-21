<div id="member-selector-widget" class="member-selector-widget">
    <div id="member-selector">
        <div class="d-flex justify-content-end">
            <div class="selected-members-info">
                <span>Please, select contractors bellow <a href="#" class="link link--orange action-clear" title="Clear selection"><i class="fas fa-eraser"></i></a></span>
            </div>
            <div class="selected-members">
                @foreach($members as $member)
                    <span class="selected-item" data-id="{{ $member->id }}">{{ $member->title }} <a class="action-delete" href="#"><i class="fas fa-backspace"></i></a></span>
                @endforeach
            </div>
            <div class="visibility">
                <div class="d-inline-block">
                    {!! Form::radio('visibility', 'private', $visibility == 'private', ['id' => 'visibility-private']) !!} <label for="visibility-private">@lang('jobs.private')</label>
                </div>
                <div class="d-inline-block ml-4">
                    {!! Form::radio('visibility', 'public', $visibility == 'public', ['id' => 'visibility-public']) !!} <label for="visibility-public">@lang('jobs.public')</label>
                </div>
            </div>
            <div class="actions">
                {{ Form::button(trans('pagination.next'), ['class' => 'btn btn-block btn--orange create-job-btn', 'data-url' => route('account.jobs.wizard.period')]) }}
            </div>
        </div>
    </div>
</div>

@section('footer_scripts')
    @parent
    <script type="text/javascript" src="{{ asset('assets/js/frontend/member-selector.js') }}"></script>
@stop