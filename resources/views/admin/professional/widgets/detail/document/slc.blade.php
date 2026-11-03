<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="row">
            @if ($professional->slc->registration_image)
            <div class="col-md-4 mb-3 text-center">
                <div class="tab-content"> <label class="form-label">Transcript Image</label>
                      <div><a href="{{ $professional->slc->full_registration_image }}"
                            data-fancybox="image"><img src="{{ $professional->slc->full_registration_image }}"></a>
                    </div>
                </div>
            </div>
            @endif
            @if ($professional->slc->transcript_image)
            <div class="col-md-4 mb-3 text-center">
                <div class="tab-content"> <label class="form-label">Transcript Image</label>
                      <div><a href="{{ $professional->slc->full_transcript_image }}"
                            data-fancybox="image"><img src="{{ $professional->slc->full_transcript_image }}"></a>
                    </div>
                </div>
            </div>
            @endif
            @if ($professional->slc->transcript_bac_1)
            <div class="col-md-4 mb-3 text-center">
                <div class="tab-content"> <label class="form-label">Transcript Image 1</label>
                      <div><a href="{{ $professional->slc->full_transcript_bac_1 }}"
                            data-fancybox="image"><img src="{{ $professional->slc->full_transcript_bac_1 }}"></a>
                    </div>
                </div>
            </div>
            @endif
            @if ($professional->slc->transcript_bac_2)
            <div class="col-md-4 mb-3 text-center">
                <div class="tab-content"> <label class="form-label">Transcript Image</label>
                      <div><a href="{{ $professional->slc->full_transcript_bac_2 }}"
                            data-fancybox="image"><img src="{{ $professional->slc->full_transcript_bac_2 }}"></a>
                    </div>
                </div>
            </div>
            @endif
            @if ($professional->slc->transcript_bac_3)
            <div class="col-md-4 mb-3 text-center">
                <div class="tab-content"> <label class="form-label">Transcript Image</label>
                      <div><a href="{{ $professional->slc->full_transcript_bac_3 }}"
                            data-fancybox="image"><img src="{{ $professional->slc->full_transcript_bac_3 }}"></a>
                    </div>
                </div>
            </div>
            @endif
            @if ($professional->slc->migration_image)
            <div class="col-md-4 mb-3 text-center">
                <div class="tab-content"> <label class="form-label">Provisional Image</label>
                    <div class="document-image"><a href="{{ $professional->slc->full_migration_image }}"
                            data-fancybox="image"><img src="{{ $professional->slc->full_migration_image }}"></a>
                    </div>
                </div>
            </div>
            @endif
            @if ($professional->slc->provisional_image)
            <div class="col-md-4 mb-3 text-center">
                <div class="tab-content"> <label class="form-label">Provisional Image</label>
                    <div class="document-image"><a href="{{ $professional->slc->full_provisional_image }}"
                            data-fancybox="image"><img src="{{ $professional->slc->full_provisional_image }}"></a>
                    </div>
                </div>
            </div>
            @endif

            @if ($professional->slc->character_image)
            <div class="col-md-4 mb-3 text-center">
                <div class="tab-content"> <label class="form-label">Character Image</label>
                      <div><a href="{{ $professional->slc->full_character_image }}"
                            data-fancybox="image"><img src="{{ $professional->slc->full_character_image }}"></a>
                    </div>
                </div>
            </div>
            @endif
            @if ($professional->slc->equivalence_certificate)
            <div class="col-md-4 mb-3 text-center">
                <div class="tab-content"> <label class="form-label">Equivalence Image</label>
                      <div><a href="{{ $professional->slc->full_equivalence_certificate }}"
                            data-fancybox="image"><img src="{{ $professional->slc->full_equivalence_certificate }}"></a>
                    </div>
                </div>
            </div>
            @endif
            @if ($professional->slc->council_registration_certificate)
            <div class="col-md-4 mb-3 text-center">
                <div class="tab-content"> <label class="form-label">Equivalence Image</label>
                      <div><a
                            href="{{ $professional->slc->full_council_registration_certificate }}"
                            data-fancybox="image"><img
                                src="{{ $professional->slc->full_council_registration_certificate }}"></a>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>