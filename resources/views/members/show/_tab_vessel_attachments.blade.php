<div class="vessel-attachments tab-content">
    <div class="section">
        @if($member->profile->publicAttachments->count())
            <div class="d-flex justify-content-start">
                @foreach($member->profile->publicAttachments as $document)
                    <a class="file" href="{{ $document->file->getPublicUrl() }}" target="_blank">
                        <span><i class="far fa-file"></i></span>
                        <span>{{ $document->file->filename }}</span>
                    </a>
                @endforeach
            </div>
        @else
            <p class="mt-3 mb-3 text-center">No attachments</p>
        @endif
    </div>
</div>