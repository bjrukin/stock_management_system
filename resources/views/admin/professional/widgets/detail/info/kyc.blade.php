@if($professional->kyc)
<div class="row">
    <div class="col-lg-4 mb-2">
        <div class="image-preview">
            <a href="{{ $professional->kyc->full_profile_img }}" data-fancybox="image"
                title="{{ $professional->kyc->full_name }}"> <img style="height:250px; width:100% ; object-fit:contain;"
                    src="{{ $professional->kyc->full_profile_img }}"></a>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <tbody>
                    <td class="fw-bold">Name</td>
                    <td>{{ $professional->kyc->name }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">DOB</td>
                        <td>{{ $professional->kyc->dob }} </td>
                    </tr>
                    <tr>
                        <td class="fw-bold" width="50%">Symbol Number</td>
                        <td width="50%">{{ $professional->kyc->symbol_number }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Registration Number</td>
                        <td>{{ $professional->kyc->registration_number }}</td>
                    </tr>
                    <tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif