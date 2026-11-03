<div class="modal-content">
    <form method="post" action="{{ route('admin.kyc.allocate.save') }}" id="allocate_form">
        @csrf
        <div class="modal-header">
            <h5 class="modal-title" id="staticBackdropLabel">{{ ucwords(strtolower($certificate->name)) }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <input name="id" value="{{ $certificate->id }}" type="hidden">
            <div class="mb-3">
                <label class="form-label">Professional *</label>
                <select class="form-select select2-ajax" name="professional_id">
                    <option value="{{ $certificate->professional_id }}">
                        {{ ucwords(strtolower(optional($certificate->professional)->name)) }}</option>
                </select>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="main_btn btn-loading" onclick="saveData(this.form)">Submit</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
    </form>
</div>