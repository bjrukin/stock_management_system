<div class="row">
    <div class="col-md-12 col-sm-12 mt-3">
        <div class="row">
            <div class="col-md-12 info-container mb-3">
                <div><strong>College Name: </strong><span>{{ $professional->tslc->college_name }}</span></div>
                <div><strong>Board: </strong><span>{{ $professional->tslc->board_university }}</span></div>
                <div><strong>Passed Year: </strong><span>{{ $professional->tslc->passed_year }}</span></div>
                <div><strong>Registration Number: </strong><span>{{ $professional->tslc->registration_number }}</span>
                </div>
            </div>
            @if($professional->tslc->transcript_image)
                <div class="col-md-4 img-container">
                    <strong>Transcript Image</strong>
                    <div class="document-image">
                        <a href="{{ $professional->tslc->full_transcript_image }}" data-fancybox="image">
                            <img src="{{ $professional->tslc->full_transcript_image }}">
                        </a>
                    </div>
                </div>
            @endif
            @if($professional->tslc->provisional_image)
                <div class="col-md-4 img-container">
                    <strong>Provisional Image</strong>
                    <div class="document-image">
                        <a href="{{ $professional->tslc->full_provisional_image }}" data-fancybox="image">
                            <img src="{{ $professional->tslc->full_provisional_image }}">
                        </a>
                    </div>
                </div>
            @endif
            @if($professional->tslc->character_image)
                <div class="col-md-4 img-container">
                    <strong>Character Image</strong>
                    <div class="document-image">
                        <a href="{{ $professional->tslc->full_character_image }}" data-fancybox="image">
                            <img src="{{ $professional->tslc->full_character_image }}">
                        </a>
                    </div>
                </div>
            @endif
            @if($professional->tslc->ojt_image)
                <div class="col-md-4 img-container">
                    <strong>OJT Image</strong>
                    <div class="document-image">
                        <a href="{{ $professional->tslc->full_ojt_image }}" data-fancybox="image">
                            <img src="{{ $professional->tslc->full_ojt_image }}">
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
