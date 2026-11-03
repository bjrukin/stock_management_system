<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="row">
            @if($applicant->voucher_image)
                <div class="col-md-4 img-container">
                    <strong>Voucher Image</strong>
                    <div class="document-image">
                        <a href="{{ $applicant->full_voucher_image}}" data-fancybox="image">
                            <img src="{{ $applicant->full_voucher_image }}">
                        </a>
                    </div>
                </div>
            </div>
            @endif
            @if($applicant->biometric_photo)
                <div class="col-md-4 img-container">
                    <strong>Biometric Photo</strong>
                    <div class="document-image">
                        <a href="{{ $applicant->full_biometric_photo }}" data-fancybox="image">
                            <img src="{{ $applicant->full_biometric_photo }}">
                        </a>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>