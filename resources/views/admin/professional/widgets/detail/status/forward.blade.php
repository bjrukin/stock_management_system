<div class="card">
    <h5 class="card-header">
    From {{  ucwords(strtolower(str_replace('_', ' ', $lastest_applicant->state))) }}  Forwarded To
    </h5>
    <div class="card-body"  style="height:280px;">
        <form action="{{ route('admin.applicant.save.state') }}" method="post" id="frm_forward">
            @csrf
            <input type="hidden" name="id" value="{{ $lastest_applicant->id }}" />
            <div class="mb-3">
                <label class="form-label">Select State</label>
                <select class="form-select select2" name="state">
                    @if($roles->isNotEmpty())
                    @foreach($roles as $role)
                    <option value="{{ strtolower(str_replace(' ', '_', $role->name)) }}" @if($lastest_applicant->state == strtolower(str_replace(' ', '_', $role->name))) selected @endif>{{ $role->name }}</option>
                    @endforeach
                    @endif
                </select>
            </div>
            <div class="mb-3"> <button type="button"
            class="main_btn btn-loading mb-3" onclick="saveForward(this.form)">Submit</button></div>
        </form>
    </div>
</div>