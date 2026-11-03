<div class="row">
    <div class="col-md-3 col-sm-12">
        <div class="card">
            <div class="card-body">
                @include('admin.professional.widgets.detail.info.profile')
            </div>
        </div>
    </div>
    <div class="col-lg-9">
        @if($professional->info)
        <div class="card">
            <div class="card-body">
                @if(auth()->guard('admin')->user()->hasPermission('Professional', 'edit'))
                <div class="float-end">
                    <a href="{{ route('admin.professional.profile',  ['id' =>$professional->id]) }}"
                        class="btn btn-primary">Edit Info</a>
                </div>
                @endif
                <ul class="nav nav-tabs" id="infoTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="personal-tab" data-bs-toggle="tab"
                            data-bs-target="#personal" type="button" role="tab" aria-controls="personal"
                            aria-selected="true">Personal Information</button>
                    </li>
                    @if($professional->kyc)
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="kyc-tab" data-bs-toggle="tab" data-bs-target="#kyc" type="button"
                            role="tab" aria-controls="kyc" aria-selected="false">KYC</button>
                    </li>
                    @endif
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="log-tab" data-bs-toggle="tab" data-bs-target="#log" type="button"
                            role="tab" aria-controls="log" aria-selected="false">Application Log</button>
                    </li>
                </ul>
                <div class="tab-content" id="infoTabContent">
                    <div class="tab-pane fade show active" id="personal" role="tabpanel" aria-labelledby="personal-tab">
                        @include('admin.professional.widgets.detail.info.personal')</div>
                    <div class="tab-pane fade fixed-height-350" id="kyc" role="tabpanel" aria-labelledby="kyc-tab">
                        @include('admin.professional.widgets.detail.info.kyc')</div>
                    <div class="tab-pane fade fixed-height-350" id="log" role="tabpanel" aria-labelledby="log-tab">
                        @include('admin.professional.widgets.detail.info.log')</div>
                </div>
            </div>
        </div>

        @endif
    </div>
</div>