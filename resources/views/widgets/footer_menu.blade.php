<div class="footer-nav-widget">
    <div class="footer-nav-container d-flex flex-row justify-content-around row">
        <div class="footer-nav-column">
            @if (isset($FooterMenu1))
                {!! $FooterMenu1->asUl(['class' => 'footer-nav list-unstyled'], ['class' => 'list-unstyled']) !!}
            @endif
            @if (isset($FooterMenu2))
                {!! $FooterMenu2->asUl(['class' => 'footer-nav list-unstyled'], ['class' => 'list-unstyled']) !!}
            @endif
        </div>
        <div class="footer-nav-column">
            @if (isset($FooterMenu3))
                {!! $FooterMenu3->asUl(['class' => 'footer-nav list-unstyled'], ['class' => 'list-unstyled']) !!}
            @endif
            @if (isset($FooterMenu4))
                {!! $FooterMenu4->asUl(['class' => 'footer-nav list-unstyled'], ['class' => 'list-unstyled']) !!}
            @endif
        </div>
        <div class="footer-nav-column">
            @if (isset($FooterMenu5))
                {!! $FooterMenu5->asUl(['class' => 'footer-nav list-unstyled'], ['class' => 'list-unstyled']) !!}
            @endif
            @if (isset($FooterMenu6))
                {!! $FooterMenu6->asUl(['class' => 'footer-nav list-unstyled'], ['class' => 'list-unstyled']) !!}
            @endif
        </div>
    </div>
</div>