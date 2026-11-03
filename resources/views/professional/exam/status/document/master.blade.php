<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="row">
            <div class="col-md-12 info-container mb-3">
                <div><strong>College Name: </strong><span>{{ $professional->master->college_name }}</span></div>
                <div><strong>University: </strong><span>{{ $professional->master->board_university }}</span></div>
                <div><strong>Admission Year: </strong><span>{{ $professional->master->admission_year }}</span></div>
                <div><strong>Passed Year: </strong><span>{{ $professional->master->passed_year }}</span></div>
                <div><strong>Registration Number: </strong><span>{{ $professional->master->registration_number }}</span>
                </div>
            </div>
            @if($professional->master->transcript_mas_marksheet)
                <div class="col-md-4 img-container">
                    <strong>Transcript Image</strong>
                    <div class="document-image">
                        <a href="{{ $professional->master->full_transcript_mas_marksheet }}" data-fancybox="image">
                            <img src="{{ $professional->master->full_transcript_mas_marksheet }}">
                        </a>
                    </div>
                </div>
            @endif
            @if($professional->master->equivalence_certificate)
                <div class="col-md-4 img-container">
                    <strong>Equivalence Image</strong>
                    <div class="document-image">
                        <a href="{{ $professional->master->full_equivalence_certificate }}" data-fancybox="image">
                            <img src="{{ $professional->master->full_equivalence_certificate }}">
                        </a>
                    </div>
                </div>
            @endif
            @if($professional->master->provisional_image)
                <div class="col-md-4 img-container">
                    <strong>Provisional Image</strong>
                    <div class="document-image">
                        <a href="{{ $professional->master->full_provisional_image }}" data-fancybox="image">
                            <img src="{{ $professional->master->full_provisional_image }}">
                        </a>
                    </div>
                </div>
            @endif
            @if($professional->master->character_image)
                <div class="col-md-4 img-container">
                    <strong>Character Image</strong>
                    <div class="document-image">
                        <a href="{{ $professional->master->full_character_image }}" data-fancybox="image">
                            <img src="{{ $professional->master->full_character_image }}">
                        </a>
                    </div>
                </div>
            @endif
            @if($professional->master->intership_image)
                <div class="col-md-4 img-container">
                    <strong>Internship Image</strong>
                    <div class="document-image">
                        <a href="{{ $professional->master->full_intership_image }}" data-fancybox="image">
                            <img src="{{ $professional->master->full_intership_image }}">
                        </a>
                    </div>
                </div>
            @endif
            @if($professional->master->passport_image)
                <div class="col-md-4 img-container">
                    <strong>NHPC permission Letter Image</strong>
                    <div class="document-image">
                        <a href="{{ $professional->master->full_noc_image }}" data-fancybox="image">
                            <img src="{{ $professional->master->full_noc_image }}">
                        </a>
                    </div>
                </div>
            @endif
            @if($professional->master->visa_image)
                <div class="col-md-4 img-container">
                    <strong>VISA Image</strong>
                    <div class="document-image">
                        <a href="{{ $professional->master->full_visa_image }}" data-fancybox="image">
                            <img src="{{ $professional->master->full_visa_image }}">
                        </a>
                    </div>
                </div>
            @endif
            @if($professional->master->passport_image)
                <div class="col-md-4 img-container">
                    <strong>Passport Image</strong>
                    <div class="document-image">
                        <a href="{{ $professional->master->full_passport_image }}" data-fancybox="image">
                            <img src="{{ $professional->master->full_passport_image }}">
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>