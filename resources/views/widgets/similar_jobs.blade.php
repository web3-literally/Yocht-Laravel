@if($jobs->count())
    <div class="similar-jobs-widget side-bar-section bar-recent-widget">
        <h4>Similar Jobs</h4>
        <ul class="list-unstyled">
            @foreach($jobs as $job)
                <li>
                    <a href="{{ route('jobs.show', $job->slug) }}">
                        <div class="image">
                            <img src="{{ $job->getThumb('150x150') }}" alt="{{ $job->title }}">
                        </div>
                        <div class="content">
                            <h5>{{ $job->title }}</h5>
                            <small class="author">Posted by {{ $job->user->full_name }}</small><br>
                            <small class="date"><i class="fa fa-calendar-alt"></i> {{ $job->updated_at->toFormattedDateString() }}</small>
                        </div>
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
@endif