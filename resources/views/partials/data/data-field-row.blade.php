<div class="container-fluid data-row mt-3">
    <div class="row">
        <div class="col-1 sortable-handle">
            <i class="fa fa-sort"></i>
        </div>
        <div class="col-4">
            <input type="text" name="data[key][]" autocomplete="off" class="form-control data-key" value="{{ $key ?? '' }}" placeholder="Key">
        </div>
        <div class="col-4">
            <input type="text" name="data[value][]" autocomplete="off" class="form-control data-value" value="{{ $value ?? '' }}" placeholder="Value">
        </div>
        <div class="col-3">
            <button class="btn data-row-delete" type="button">
                Delete
            </button>
        </div>
    </div>
</div>