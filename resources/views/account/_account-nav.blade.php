<ul class="account-nav btn-group" role="group" aria-label="Account">
    <li class="nav-item">
        <a href="{{ route('account.overview') }}" class="btn btn-default">@lang('general.overview')</a>
    </li>
    @if(Sentinel::getUser()->hasAccess(['billing.payment-methods']))
        <li class="nav-item">
            <a href="{{ route('payment-methods') }}" class="btn {!! Request::is('*/account/payment-methods*') ? 'btn-primary' : 'btn-default' !!}">@lang('billing.payment_methods')</a>
        </li>
    @endif
    @if(Sentinel::getUser()->hasAccess(['billing.subscriptions']))
        <li class="nav-item">
            <a href="{{ route('subscriptions') }}" class="btn {!! Request::is('*/account/subscriptions*') ? 'btn-primary' : 'btn-default' !!}">@lang('billing.subscriptions')</a>
        </li>
    @endif
    @if(Sentinel::getUser()->hasAccess(['billing.invoices']))
        <li class="nav-item">
            <a href="{{ route('invoices') }}" class="btn {!! Request::is('*/account/invoices*') ? 'btn-primary' : 'btn-default' !!}">@lang('billing.invoices')</a>
        </li>
    @endif
    <li class="nav-item">
        <a href="{{ route('account.change-password') }}" class="btn {!! Request::is('*/account/change-password*') ? 'btn-primary' : 'btn-default' !!}">@lang('general.account_change_password')</a>
    </li>
</ul>