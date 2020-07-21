<div class="message">
    <span class="photo">
        <img src="{{ $message->user->getThumb('53x53') }}" alt="{{ $message->user->full_name }}">
    </span>
    <h4 class="user-name">{{ $message->user->full_name }}</h4>
    <small>{{ $message->created_at->diffForHumans() }}</small>
    <div class="card bg-faded">
        <div class="text card-block">
            {!! nl2br($message->body) !!}
        </div>
    </div>
</div>