<div class="inline-block">
    <ul class="profile-nav btn-group nav nav-tabs" role="group" aria-label="Profile">
        @foreach($stepsList as $step => $label)
            <li class="nav-item">
                <a href="{{ route('account.boat.transfer.step', ['boat_id' => $currentBoat->id, 'step' => $step]) }}" class="btn {{ $step > $processStep ? 'disabled' : '' }} {!! Request::is('*/step/'.$step) ? 'btn-primary' : 'btn-default' !!}">{{ $label }}</a>
            </li>
        @endforeach
    </ul>
</div>