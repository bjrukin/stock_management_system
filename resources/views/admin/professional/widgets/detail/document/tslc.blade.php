<div class="row">
    <div class="col-md-12 col-sm-12  mt-3">
        <div class="row">
            @if ($professional->tslc->transcript_image)
            <div class="col-md-4 img-container">
                <strong>Transcript Image</strong>
                <div><a href="{{ $professional->tslc->full_transcript_image }}" data-fancybox="image"><img
                            src="{{ $professional->tslc->full_transcript_image }}"></a>
                </div>
            </div>
            @endif
            @if ($professional->tslc->transcript_bac_1)
            <div class="col-md-4 img-container">
                <strong>Transcript Image 1</strong>
                <div><a href="{{ $professional->tslc->full_transcript_bac_1 }}" data-fancybox="image"><img
                            src="{{ $professional->tslc->full_transcript_bac_1 }}"></a>
                </div>
            </div>
            @endif
            @if ($professional->tslc->transcript_bac_2)
            <div class="col-md-4 img-container">
                <strong>Transcript Image 2</strong>
                <div><a href="{{ $professional->tslc->full_transcript_bac_2 }}" data-fancybox="image"><img
                            src="{{ $professional->tslc->full_transcript_bac_2 }}"></a>
                </div>
            </div>
            @endif
            @if ($professional->tslc->transcript_bac_3)
            <div class="col-md-4 img-container">
                <strong>Transcript Image 3</strong>
                <div><a href="{{ $professional->tslc->full_transcript_bac_3 }}" data-fancybox="image"><img
                            src="{{ $professional->tslc->full_transcript_bac_3 }}"></a>
                </div>
            </div>
            @endif
            @if ($professional->tslc->provisional_image)
            <div class="col-md-4 img-container">
                <strong>Provisional Image</strong>
                <div><a href="{{ $professional->tslc->full_provisional_image }}" data-fancybox="image"><img
                            src="{{ $professional->tslc->full_provisional_image }}"></a>
                </div>
            </div>
            @endif
            @if ($professional->tslc->character_image)
            <div class="col-md-4 img-container">
                <strong>Character Image</strong>
                <div><a href="{{ $professional->tslc->full_character_image }}" data-fancybox="image"><img
                            src="{{ $professional->tslc->full_character_image }}"></a>
                </div>
            </div>
            @endif
            @if ($professional->tslc->ojt_image)
            <div class="col-md-4 img-container">
                <strong>OJT Image</strong>
                <div><a href="{{ $professional->tslc->full_ojt_image }}" data-fancybox="image"><img
                            src="{{ $professional->tslc->full_ojt_image }}"></a>
                </div>
            </div>
            @endif
            @if ($professional->tslc->intership_image)
            <div class="col-md-4 img-container">
                <strong>Internship Image</strong>
                <div><a href="{{ $professional->tslc->full_intership_image }}" data-fancybox="image"><img
                            src="{{ $professional->tslc->full_intership_image }}"></a>
                </div>
            </div>
            @endif
            @if ($professional->tslc->noc_image)
            <div class="col-md-4 img-container">
                <strong>NOC Image</strong>
                <div><a href="{{ $professional->tslc->full_noc_image }}" data-fancybox="image"><img
                            src="{{ $professional->tslc->full_noc_image }}"></a>
                </div>
            </div>
            @endif
            @if ($professional->tslc->migration_image)
            <div class="col-md-4 img-container">
                <strong>Migration Image</strong>
                <div><a href="{{ $professional->tslc->migration_image }}" data-fancybox="image"><img
                            src="{{ $professional->tslc->migration_image }}"></a>
                </div>
            </div>
            @endif
            @if ($professional->tslc->migration_image)
            <div class="col-md-4 img-container">
                <strong>Migration Image</strong>
                <div><a href="{{ $professional->tslc->full_migration_image }}" data-fancybox="image"><img
                            src="{{ $professional->tslc->full_migration_image }}"></a>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>