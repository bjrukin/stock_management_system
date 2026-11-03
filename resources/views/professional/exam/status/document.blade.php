<ul class="nav nav-tabs" id="documentTab" role="tablist">
    @if($professional->info)
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="profile-tab" data-bs-toggle="tab" data-bs-target="#voucher" type="button"
                role="tab" aria-controls="Voucher" aria-selected="true">Voucher Details</button>
        </li>
    @endif
    @if($professional->info)
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="supportive-tab" data-bs-toggle="tab" data-bs-target="#supportive" type="button"
                role="tab" aria-controls="supportive" aria-selected="false">Supportive Documents</button>
        </li>
    @endif
    @if($professional->slc)
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#slc" type="button" role="tab"
                aria-controls="slc" aria-selected="false">SLC / SEE Documents</button>
        </li>
    @endif
    @if($professional->tslc)
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#tslc" type="button" role="tab"
                aria-controls="tslc" aria-selected="false">TSLC Documents</button>
        </li>
    @endif
    @if($professional->pcl)
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#pcl" type="button" role="tab"
                aria-controls="pcl" aria-selected="false">PCL / +2 Documents</button>
        </li>
    @endif
    @if($professional->bachelor)
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#bachelor" type="button"
                role="tab" aria-controls="bachelor" aria-selected="false">Bachelor Documents</button>
        </li>
    @endif
    @if($professional->master)
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#master" type="button" role="tab"
                aria-controls="master" aria-selected="false">Master Documents</button>
        </li>
    @endif
</ul>

<div class="tab-content" id="documentTabContent">
    @if($professional->info)
        <div class="tab-pane fade show active" id="voucher" role="tabpanel" aria-labelledby="voucher-tab">
            @include('professional.exam.status.document.voucher')
        </div>
    @endif
    @if($professional->info)
        <div class="tab-pane fade" id="supportive" role="tabpanel" aria-labelledby="supportive-tab">
            @include('professional.exam.status.document.supportive')
        </div>
    @endif
    @if($professional->slc)
        <div class="tab-pane fade" id="slc" role="tabpanel" aria-labelledby="slc-tab">
            @include('professional.exam.status.document.slc')
        </div>
    @endif
    @if($professional->tslc)
        <div class="tab-pane fade" id="tslc" role="tabpanel" aria-labelledby="tslc-tab">
            @include('professional.exam.status.document.tslc')
        </div>
    @endif
    @if($professional->pcl)
        <div class="tab-pane fade" id="pcl" role="tabpanel" aria-labelledby="pcl-tab">
            @include('professional.exam.status.document.pcl')
        </div>
    @endif
    @if($professional->bachelor)
        <div class="tab-pane fade" id="bachelor" role="tabpanel" aria-labelledby="bachelor-tab">
            @include('professional.exam.status.document.bachelor')
        </div>
    @endif
    @if($professional->master)
        <div class="tab-pane fade" id="master" role="tabpanel" aria-labelledby="master-tab">
            @include('professional.exam.status.document.master')
        </div>
    @endif
</div>