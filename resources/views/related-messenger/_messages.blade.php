@php($unReadedMessages = $unReadedMessages ?? Sentinel::getUser()->unreadMessagesCount())
@if ($unReadedMessages)
    <ul class="list-unstyled">
        @foreach (Sentinel::getUser()->unreadThreads(6) as $thread)
            <li>
                <a href="{{ route('account.messages.show', $thread->id) }}" class="d-block">
                    <div class="title"><strong>{!! HtmlTruncator::truncate($thread->latestMessage->body, 32) !!}</strong></div>
                    <small>@lang('notification.by', ['full_name' => $thread->directUser()->full_name])</small> <small class="pull-right">@lang('notification.time', ['time' => $thread->latestMessage->created_at->diffForHumans() ])</small>
                </a>
            </li>
        @endforeach
    </ul>
    <a href="{{ route('account.messages.index') }}" class="view-all">
        @lang('message.view_all')
    </a>
@endif