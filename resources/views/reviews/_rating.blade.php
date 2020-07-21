@php($level = $level ?? 1)
@if(isset($rating) && $rating)
    <div class="rating level-{{ $level }}">
        <ul class="list-unstyled">
            @for ($i = 1; $i <= 5; $i++)
                <li class="d-inline selected">
                    @php($k = $i - $rating)
                    @if ($i <= $rating)
                        <i class="fas fa-star"></i>
                    @elseif ($k > 0 && $k < 1)
                        <i class="fas fa-star-half-alt"></i>
                    @else
                        <i class="far fa-star"></i>
                    @endif
                </li>
            @endfor
        </ul>
    </div>
@else
    <div class="rating">
        <p>Not rated yet</p>
    </div>
@endif