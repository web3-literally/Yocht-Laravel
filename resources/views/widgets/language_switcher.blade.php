<ul id="language-switcher" class="navbar-nav">
    <li class="nav-item language-switcher-item">
        <a href="#" role="button" class="nav-link dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{ $currentLocaleName }}</a>
        <ul class="dropdown-menu">
            @foreach($supportedLocales as $localeCode => $properties)
                <li class="nav-item">
                    <a rel="alternate" hreflang="{{ str_replace('_', '-', $properties['regional']) }}" href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}" class="nav-link">
                        {{ $properties['native'] }}
                    </a>
                </li>
            @endforeach
        </ul>
    </li>
</ul>