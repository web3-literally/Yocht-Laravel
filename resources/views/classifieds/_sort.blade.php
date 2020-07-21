<ul class="listing-order navbar-nav">
    <li class="nav-item language-switcher-item">
        <a href="#" role="button" class="nav-link dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Sort
            by {{ $orders[$sortBy] }}</a>
        <ul class="dropdown-menu">
            <li class="nav-item">
                @foreach($orders as $value => $title)
                    @if($sortBy != $value)
                        <a href="{{ app('request')->fullUrlWithQuery(['order' => $value]) }}" class="nav-link">
                            <span>{{ $title }}</span>
                            @if ($value == 'name')
                                <i class="fas fa-sort-alpha-down"></i>
                            @endif
                            @if (strpos($value, 'asc')!==false)
                                <i class="fas fa-sort-amount-down"></i>
                            @endif
                            @if (strpos($value, 'desc')!==false)
                                <i class="fas fa-sort-amount-up"></i>
                            @endif
                        </a>
                    @endif
                @endforeach
            </li>
        </ul>
    </li>
</ul>