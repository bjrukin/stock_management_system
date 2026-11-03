<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="row">
            @if ($professional->bachelor->transcript_image)
            <div class="col-md-4 mb-3 text-center">
                <div class="tab-content">
                    <label class="form-label">Transcript Image </label>
                    <div><a href="{{ $professional->bachelor->full_transcript_image }}" data-fancybox="image"><img
                                src="{{ $professional->bachelor->full_transcript_image }}"></a> </div>
                </div>
            </div>
            @endif
            @if ($professional->bachelor->transcript_bac_1)
            <div class="col-md-4 mb-3 text-center">
                <div class="tab-content">
                    <label class="form-label">Transcript Image 1</label>
                    <div><a href="{{ $professional->bachelor->full_transcript_bac_1 }}" data-fancybox="image"><img
                                src="{{ $professional->bachelor->full_transcript_bac_1 }}"></a> </div>
                </div>
            </div>
            @endif
            @if ($professional->bachelor->transcript_bac_2)
            <div class="col-md-4 mb-3 text-center">
                <div class="tab-content">
                    <label class="form-label">Transcript Image 2</label>
                    <div><a href="{{ $professional->bachelor->full_transcript_bac_2 }}" data-fancybox="image"><img
                                src="{{ $professional->bachelor->full_transcript_bac_2 }}"></a> </div>
                </div>
            </div>
            @endif
            @if ($professional->bachelor->transcript_bac_3)
            <div class="col-md-4 mb-3 text-center">
                <div class="tab-content">
                    <label class="form-label">Transcript Image 3</label>
                    <div><a href="{{ $professional->bachelor->full_transcript_bac_3 }}" data-fancybox="image"><img
                                src="{{ $professional->bachelor->full_transcript_bac_3 }}"></a> </div>
                </div>
            </div>
            @endif
            @if ($professional->bachelor->transcript_bac_4)
            <div class="col-md-4 mb-3 text-center">
                <div class="tab-content">
                    <label class="form-label">Transcript Image 4</label>
                    <div><a href="{{ $professional->bachelor->full_transcript_bac_4 }}" data-fancybox="image"><img
                                src="{{ $professional->bachelor->full_transcript_bac_4 }}"></a> </div>
                </div>
            </div>
            @endif
            @if ($professional->bachelor->transcript_bac_5)
            <div class="col-md-4 mb-3 text-center">
                <div class="tab-content">
                    <label class="form-label">Transcript Image 5</label>
                    <div><a href="{{ $professional->bachelor->full_transcript_bac_5 }}" data-fancybox="image"><img
                                src="{{ $professional->bachelor->full_transcript_bac_5 }}"></a> </div>
                </div>
            </div>
            @endif
            @if ($professional->bachelor->transcript_bac_6)
            <div class="col-md-4 mb-3 text-center">
                <div class="tab-content">
                    <label class="form-label">Transcript Image 6</label>
                    <div><a href="{{ $professional->bachelor->full_transcript_bac_6 }}" data-fancybox="image"><img
                                src="{{ $professional->bachelor->full_transcript_bac_6 }}"></a> </div>
                </div>
            </div>
            @endif
            @if ($professional->bachelor->transcript_bac_7)
            <div class="col-md-4 mb-3 text-center">
                <div class="tab-content">
                    <label class="form-label">Transcript Image 7</label>
                    <div><a href="{{ $professional->bachelor->full_transcript_bac_7 }}" data-fancybox="image"><img
                                src="{{ $professional->bachelor->full_transcript_bac_7 }}"></a> </div>
                </div>
            </div>
            @endif
            @if ($professional->bachelor->transcript_bac_8)
            <div class="col-md-4 mb-3 text-center">
                <div class="tab-content">
                    <label class="form-label">Transcript Image 8</label>
                    <div><a href="{{ $professional->bachelor->full_transcript_bac_8 }}" data-fancybox="image"><img
                                src="{{ $professional->bachelor->full_transcript_bac_8 }}"></a> </div>
                </div>
            </div>
            @endif
            @if ($professional->bachelor->equivalence_certificate)
            <div class="col-md-4 mb-3 text-center">
                <div class="tab-content">
                    <label class="form-label">Equivalence Certificate</label>
                    <div><a href="{{ $professional->bachelor->full_equivalence_certificate }}"
                            data-fancybox="image"><img
                                src="{{ $professional->bachelor->full_equivalence_certificate }}"></a> </div>
                </div>
            </div>
            @endif
            @if ($professional->bachelor->provisional_image)
            <div class="col-md-4 mb-3 text-center">
                <div class="tab-content">
                    <label class="form-label">Provisional Image</label>
                    <div><a href="{{ $professional->bachelor->full_provisional_image }}" data-fancybox="image"><img
                                src="{{ $professional->bachelor->full_provisional_image }}"
                                id="display_provisional_image"></a> </div>
                </div>
            </div>
            @endif
            @if ($professional->bachelor->migration_image)
            <div class="col-md-4 mb-3 text-center">
                <div class="tab-content">
                    <label class="form-label">Migration Image</label>
                    <div><a href="{{ $professional->bachelor->full_migration_image }}" data-fancybox="image"><img
                                src="{{ $professional->bachelor->full_migration_image }}"></a> </div>
                </div>
            </div>
            @endif
            @if ($professional->bachelor->character_image)
            <div class="col-md-4 mb-3 text-center">
                <div class="tab-content">
                    <label class="form-label">Character Image</label>
                    <div><a href="{{ $professional->bachelor->full_character_image }}" data-fancybox="image"><img
                                src="{{ $professional->bachelor->full_character_image }}"></a> </div>
                </div>
            </div>
            @endif
            @if ($professional->bachelor->intership_image)
            <div class="col-md-4 mb-3 text-center">
                <div class="tab-content">
                    <label class="form-label">Internship Image</label>
                    <div><a href="{{ $professional->bachelor->full_intership_image }}" data-fancybox="image"><img
                                src="{{ $professional->bachelor->full_intership_image }}"></a> </div>
                </div>
            </div>
            @endif
            @if ($professional->bachelor->noc_image)
            <div class="col-md-4 mb-3 text-center">
                <div class="tab-content">
                    <label class="form-label">NOC Image</label>
                    <div><a href="{{ $professional->bachelor->full_noc_image }}" data-fancybox="image"><img
                                src="{{ $professional->bachelor->full_noc_image }}"></a> </div>
                </div>
            </div>
            @endif
            @if ($professional->bachelor->visa_image)
            <div class="col-md-4 mb-3 text-center">
                <div class="tab-content">
                    <label class="form-label">VISA Image</label>
                    <div><a href="{{ $professional->bachelor->full_visa_image }}" data-fancybox="image"><img
                                src="{{ $professional->bachelor->full_visa_image }}"></a> </div>
                </div>
            </div>
            @endif
            @if ($professional->bachelor->passport_image)
            <div class="col-md-4 mb-3 text-center">
                <div class="tab-content">
                    <label class="form-label">Passport Image</label>
                    <div><a href="{{ $professional->bachelor->full_passport_image }}" data-fancybox="image"><img
                                src="{{ $professional->bachelor->full_passport_image }}"></a> </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>