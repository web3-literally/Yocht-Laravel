@if($archive)
    <div class="bar-archive-widget side-bar-section">
        <h4 class="clearfix collapsed" data-toggle="collapse" href="#collapse-archive-list" role="button" aria-expanded="false">@lang('blog/title.archive')<span class="toggle-icon"><i class="fas fa-folder-open"></i></span></h4>
        <ul id="collapse-archive-list" class="collapse list-unstyled">
            @foreach($archive as $date)
                <li>
                    <span><a href="{{ route('blog', ['month' => $date->year . '-' . sprintf('%02d', $date->month)]) }}">{{ \Carbon\Carbon::createFromDate($date->year, $date->month)->format('F Y') }}</a></span>
                </li>
            @endforeach
        </ul>
    </div>
@endif