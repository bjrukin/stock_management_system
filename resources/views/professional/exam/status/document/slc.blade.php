<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="row">
            <div class="col-md-12 info-container mb-3">
                <div><strong>School Name: </strong><span>{{ $professional->slc->college_name }}</span></div>
                <div><strong>Board: </strong><span>{{ $professional->slc->board_university }}</span></div>
                <div><strong>Passed Year: </strong><span>{{ $professional->slc->passed_year }}</span></div>
            </div>
            @if($professional->slc->transcript_image)
                <div class="col-md-4 img-container">
                    <strong>Transcript Image</strong>
                    <div class="document-image">
                        <a href="{{ $professional->slc->full_transcript_image }}" data-fancybox="image">
                            <img src="{{ $professional->slc->full_transcript_image }}">
                        </a>
                    </div>
                </div>
            @endif
            @if($professional->slc->provisional_image)
                <div class="col-md-4 img-container">
                    <strong>Provisional Image</strong>
                    <div class="document-image">
                        <a href="{{ $professional->slc->full_provisional_image }}" data-fancybox="image">
                            <img src="{{ $professional->slc->full_provisional_image }}">
                        </a>
                    </div>
                </div>
            @endif
            @if($professional->slc->character_image)
                <div class="col-md-4 img-container">
                    <strong>Character Image</strong>
                    <div class="document-image">
                        <a href="{{ $professional->slc->full_character_image }}" data-fancybox="image">
                            <img src="{{ $professional->slc->full_character_image }}">
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>