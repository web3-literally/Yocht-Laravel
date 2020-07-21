@php($id = $member->parent_id ? $member->parent_id : $member->id)
@if($id == Sentinel::getUser()->getUserId())
    <div class="actions">
        @php($contactToUrl = route('members.contact-to', ['id' => $id, 'return' => route('members.show', $id)]))
        @if(\App\Helpers\Permissions::canContactTo())
            <a href="{{ $contactToUrl }}" class="contact-now btn btn--orange">@lang('general.contact_now')</a>
        @else
            <a href="{{ $contactToUrl }}" class="contact-now btn btn--orange disabled">@lang('general.contact_now')</a>
        @endif

        @php($reviewUrl = route('members.review', ['id' => $member->id]))
        @if(Sentinel::check() && !Sentinel::getUser()->hasMembership())
            <a href="{{ $reviewUrl }}" class="write-review link link--orange disabled">@lang('reviews.post_a_review')</a>
        @else
            <a href="{{ $reviewUrl }}" class="write-review link link--orange">@lang('reviews.post_a_review')</a>
        @endif
    </div>
@endif