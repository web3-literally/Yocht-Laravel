<div class="thread">
    <a href="{{ route('account.messages.show', $thread->id) }}" class="d-block mb-4">
        <span class="photo">
            @include('partials._user-status', ['instance' => $thread->directUser()])
            <img src="{{ $thread->directUser()->getThumb('53x53') }}" alt="{{ $thread->directUser()->full_name }}">
        </span>
        <h3 class="user-name">
            @if($thread->classified)
                <span class="badge badge-info label float-right">{{ $thread->classified->classified->title }}</span>
            @endif
            {{ $thread->directUser()->full_name }}
            @if($thread->isUnread(Sentinel::getUser()->getUserId()))
                <small title="Unread"><i class="fas fa-asterisk"></i></small>
            @endif
        </h3>
        <small>{{ $thread->latestMessage->created_at->diffForHumans() }}</small>
        <p class="last-message clearfix">
            {!! HtmlTruncator::truncate($thread->latestMessage->body, 32) !!}
        </p>
    </a>
</div>