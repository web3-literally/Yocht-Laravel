@if($instance->isOnline())
    <span class="status online" title="Online"></span>
@else
    <span class="status offline" title="Offline"></span>
@endif