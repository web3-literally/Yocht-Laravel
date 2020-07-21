<div class="social-links-widget">
    <ul class="list-unstyled">
        @foreach($links as $id => $link)
            <li>
                <a href="{{ $link }}" target="_blank">
                    <? if ($id == "facebook") {?>
                        <i class="fab fa-facebook-f"></i>
                    <? }
                    else { ?>
                        <i class="fab fa-{{ $id }}"></i>
                    <?} ?>
                </a>
            </li>
        @endforeach
    </ul>
</div>