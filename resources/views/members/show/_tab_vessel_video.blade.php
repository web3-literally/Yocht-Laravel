<div class="vessel-videos tab-content">
    <div class="section">
        @if($member->profile->video)
            <div class="video-container text-center">
                <video class="vid w-75" controls>
                    <source src="{{ $member->profile->video->file->getFileUrl() }}" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            </div>
        @else
            <p class="mt-3 mb-3 text-center">No videos</p>
        @endif
    </div>
</div>