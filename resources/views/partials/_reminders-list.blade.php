<ul class="list-unstyled" id="custom-scroll">
    @foreach($events as $item)
        <li>
            @if ($item instanceof \App\Models\Jobs\JobTickets)
                <div>
                    <span class="point"></span>
                    <span class="time no-wrap">Start day</span>
                    <span class="time-interval"></span>
                </div>
                <div>
                    <label>
                        @if($user->isMemberOwnerAccount() || $user->isCaptainAccount())
                            <a href="{{ route('account.jobs.edit', ['boat_id' => $item->job->vessel_id, 'id' => $item->job_id]) }}">{{ $item->job->title }}</a>
                        @else
                            <a href="{{ route('account.tickets.details', $item->id) }}">{{ $item->job->title }}</a>
                        @endif
                    </label>
                    <p>
                        {!! HtmlTruncator::truncate(strip_tags($item->job->content), 16) !!}
                    </p>
                </div>
            @endif
            @if ($item instanceof \App\Models\Events\Event)
                <div>
                    <span class="point"></span>
                    <span class="time">{{ $item->starts_at->format('G:i') }}</span>
                    <span class="time-interval">{{ $item->starts_at->format('a') }}</span>
                </div>
                <div>
                    <label>
                        @if($user->id == $item->user_id)
                            <a href="{{ route('account.events.edit', $item->id) }}">{{ $item->title }}</a>
                        @else
                            <a href="{{ route('events.show', $item->id) }}">{{ $item->title }}</a>
                        @endif
                    </label>
                    <p>
                        {!! HtmlTruncator::truncate($item->description, 16) !!}
                    </p>
                </div>
            @endif
        </li>
    @endforeach
</ul>