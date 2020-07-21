@if($member->isMemberAccount())
    <div class="item-links">
        <ul class="list-unstyled">
            @if($member->profile->link_website)
                <li><a href="{{ $member->profile->link_website }}" target="_blank"><i class="fas fa-link"></i></a></li>
            @endif
            @if($member->profile->link_blog)
                <li><a href="{{ $member->profile->link_blog }}" target="_blank"><i class="fab fa-blogger-b"></i></a></li>
            @endif
            @if($member->profile->link_youtube)
                <li><a href="{{ $member->profile->link_youtube }}" target="_blank"><i class="fab fa-youtube"></i></a></li>
            @endif
            @if($member->profile->link_pinterest)
                <li><a href="{{ $member->profile->link_pinterest }}" target="_blank"><i class="fab fa-pinterest"></i></a></li>
            @endif
            @if($member->profile->link_twitter)
                <li><a href="{{ $member->profile->link_twitter }}" target="_blank"><i class="fab fa-twitter"></i></a></li>
            @endif
            @if($member->profile->link_facebook)
                <li><a href="{{ $member->profile->link_facebook }}" target="_blank"><i class="fab fa-facebook"></i></a></li>
            @endif
            @if($member->profile->link_linkedin)
                <li><a href="{{ $member->profile->link_linkedin }}" target="_blank"><i class="fab fa-linkedin"></i></a></li>
            @endif
            @if($member->profile->link_google_plus)
                <li><a href="{{ $member->profile->link_google_plus }}" target="_blank"><i class="fab fa-google-plus"></i></a></li>
            @endif
            @if($member->profile->link_instagram)
                <li><a href="{{ $member->profile->link_instagram }}" target="_blank"><i class="fab fa-instagram"></i></a></li>
            @endif
        </ul>
    </div>
@endif