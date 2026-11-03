@if ($lastest_applicant)
<div class="row">
    <div class="col-md-6 col-sm-12">
        @include('admin.professional.widgets.detail.status.accept_reject')
    </div>
    <div class="col-lg-6">
        @include('admin.professional.widgets.detail.status.forward')
    </div>
</div>
@endif