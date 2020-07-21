<div class="full-contact-widget contact-info">
    <h2>@lang('general.contact_us')</h2>
    <div class="contact-section">
        <div class="media-body ml-3">
            <div class="title-section">
                <i class="fas fa-map-marked-alt"></i>
                <h4 class="media-heading">Address</h4>
            </div>
            <address>
                {{ Setting::get('contact.address') }}
            </address>
        </div>
    </div>
    <div class="contact-section">
        <div class="media-body ml-3">
            <div class="title-section">
                <i class="fas fa-phone"></i>
                <h4 class="media-heading">Phone</h4>
            </div>
            <ul class="list-unstyled">
                <li>
                    <span>Mobile:</span> <a href="tel:{{ Setting::get('contact.phone', '+44 (0) 000 000 0000') }}">{{ Setting::get('contact.phone', '+44 (0) 000 000 0000') }}</a>
                </li>
                <li>
                    <span>Hotline:</span> <a href="tel:{{ Setting::get('contact.phone2', '1009 678 456') }}">{{ Setting::get('contact.phone2', '1009 678 456') }}</a>
                </li>
            </ul>
        </div>
    </div>
    <div class="contact-section">
        <div class="media-body ml-3">
            <div class="title-section">
                <i class="fas fa-envelope"></i>
                <h4 class="media-heading">E-mail</h4>
            </div>
            <ul class="list-unstyled">
                <li>
                    {!! Html::mailto(Setting::get('contact.email', 'theyacht@hotmail.co.za')) !!}
                </li>
                <li>
                    {!! Html::mailto(Setting::get('contact.email2', 'support@yachtservice.com')) !!}
                </li>
            </ul>
        </div>
    </div>
</div>