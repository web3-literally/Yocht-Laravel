<div class="contact-widget">
    <h4>@lang('general.contact')</h4>
    <ul class="list-unstyled">
        <li><i class="fa fa-map-marker-alt"></i> <span>{{ Setting::get('contact.address') }}</span></li>
        <li><i class="fa fa-envelope"></i> {!! Html::mailto(Setting::get('contact.email')) !!}</li>
        <li><i class="fa fa-phone"></i> <a href="tel:{{ Setting::get('contact.phone') }}">{{ Setting::get('contact.phone') }}</a></li>
    </ul>
</div>