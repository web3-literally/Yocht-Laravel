<div class="dock-overview tab-content">
    <div class="section">
        <h5>Details</h5>
        @if($member->profile->personal_quote)
            <div class="d-flex justify-content-start">
                <div class="mr-3">
                    <label>Personal Quote/motto</label><br>
                    <blockquote>{{ $member->profile->personal_quote }}</blockquote>
                </div>
            </div>
        @endif
        <div class="d-flex justify-content-start">
            <div class="mr-3">
                <label>Established since</label><br>
                {{ $member->profile->established_year }}
            </div>
        </div>
        <div class="d-flex justify-content-start">
            <div class="mr-3">
                <label>Hours of Operation</label><br>
                {!! nl2br($member->profile->hours_of_operation) !!}
            </div>
        </div>
        <div class="d-flex justify-content-start">
            <div class="mr-3">
                <label>Accepted Forms of Payments</label><br>
                {!! nl2br($member->profile->accepted_forms_of_payments) !!}
            </div>
            <div class="mr-3">
                <label>Credentials</label><br>
                {!! nl2br($member->profile->credentials) !!}
            </div>
        </div>
        <div class="d-flex justify-content-start">
            <div class="mr-3">
                <label>Honors & Awards</label><br>
                {!! nl2br($member->profile->honors_and_awards) !!}
            </div>
        </div>
    </div>
    <div class="section item-about">
        <h5>@lang('general.account_about')</h5>
        <div class="about">
            @if($member->profile->about)
                {!! $member->profile->about !!}
            @endif
            @if($member->profile->file_id)
                <a class="download_cv link link--orange" href="{{ $member->profile->file->getPublicUrl() }}"><i class="fas fa-paperclip"></i> {{ $member->profile->file->filename }}</a>
            @endif
        </div>
    </div>
</div>