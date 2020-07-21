<div class="text-center">
    <div class="profile-image"><div class="img"><img src="{{ $currentBoat->getThumb('170x170') }}" alt="{{ $currentBoat->name }}"></div></div>
    <h1>{{ $currentBoat->name }}</h1>
    <h4>Vessel ID #{{ $currentBoat->getMemberId() }}</h4>
</div>
@if($currentBoat->crew->count())
    <p class="mt-3 mb-3">
        Vessel will be transferred with jobs, document storage, reminder management and followed crew:
    </p>
    <div class="d-flex justify-content-start flex-wrap">
        @foreach($currentBoat->crew as $link)
            <strong class="mr-3 no-wrap">{{ $link->user->full_name }}</strong>
        @endforeach
    </div>
@else
    <p class="mt-3 mb-3">
        Vessel will be transferred with jobs, document storage, reminder management.
    </p>
@endif
@if(isset($currentBoatTenders) && $currentBoatTenders->count())
    <p class="mt-3 mb-3">
        Also vessel will be transferred with followed tenders:
    </p>
    <div class="d-flex justify-content-start flex-wrap">
        @foreach($currentBoatTenders as $tender)
            <strong class="mr-3 no-wrap">{{ $tender->name }}</strong>
        @endforeach
    </div>
@endif
@if(isset($transferDate) && $transferDate)
    <p class="mt-3 mb-3">
        Vessel transfer will be made at <strong>{{ (new Carbon\Carbon($transferDate)) }}</strong>
    </p>
@endif