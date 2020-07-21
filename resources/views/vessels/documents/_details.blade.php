<div class="documents-details">
    <h4>@lang('general.details')</h4>
    <div class="r">
        <label class="l">Size</label>
        <span class="v">{{ $document->file->size_title }}</span>
    </div>
    <div class="r">
        <label class="l">Created</label>
        <span class="v">{{ $document->file->created_at->toFormattedDateString() }}</span>
    </div>
    <div class="r">
        <label class="l">Modified</label>
        <span class="v">{{ $document->file->updated_at->diffForHumans() }}</span>
    </div>
    <div class="r">
        <label class="l">Mime type</label>
        <span class="v">{{ $document->file->mime }}</span>
    </div>
    @if ($document->access_mode == 'full')
        <div class="documents-permissions mt-5">
            <h4>@lang('permissions.permissions')</h4>
            <p>Choose members to grant read access</p>
            {!! Form::select('permissions', $crewDropdown, $canRead, ['class' => 'form-control', 'id' => 'documents-permissions-input', 'multiple' => 'multiple', 'data-store-url' => route('account.documents.permission', ['id' => $document->id])]) !!}
        </div>
    @endif
</div>