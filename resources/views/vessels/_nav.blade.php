@php($tab = $tab ?? 'vessel')
<ul class="nav nav-tabs">
    <li class="nav-item"><a href="{{ route('account.vessels.add') }}" class="nav-link {{ $tab == 'vessel' ? 'active show' : '' }}">Add a Vessel</a></li>
    <li class="nav-item"><a href="{{ route('account.tenders.add') }}" class="nav-link {{ $tab == 'tender' ? 'active show' : '' }}">Add a Tender</a></li>
</ul>