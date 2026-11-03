<div class="card">
    <h5 class="card-header">
        Accept / Reject
    </h5>
    <div class="card-body">
        <form enctype="multipart/form-data" method="post" action="{{ route('admin.applicant.save.status') }}" id="frm_status">
            @csrf
            <input type="hidden" name="id" value="{{ $lastest_applicant->id }}" />
            <div class="mb-3">
                <label class="form-label">Remarks</label>
                <textarea class="form-control" name="remarks" rows="2" maxlength="500"></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Status</label>
                <select class="form-select select2" name="status">
                    <option value="accepted">Accepted</option>
                    <option value="pending">Pending</option>
                    <option value="progress">Progress</option>
                    <option value="rejected">Rejected</option>
                    <option value="onhold">Hold</option>
                    <option value="re-check">Re-Check</option>
                    <option value="re-exam">Re-exam</option>
                </select>
            </div>
            <div class="mb-3"><button type="button" class="main_btn btn-loading mb-3"
                    onclick="saveStatus(this.form)">Submit</button></div>
        </form>
    </div>
</div>