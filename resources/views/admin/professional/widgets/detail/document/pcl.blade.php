<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="row">
            @if ($professional->pcl->transcript_image)
            <div class="col-md-4 mb-3 text-center">
                <div class="tab-content"> <label class="form-label">Transcript Image</label>
                      <div><a href="{{ $professional->pcl->full_transcript_image }}" data-fancybox="image"><img
                                src="{{ $professional->pcl->full_transcript_image }}"></a>
                    </div>
                </div>
            </div>
            @endif
            @if ($professional->pcl->transcript_bac_1)
            <div class="col-md-4 mb-3 text-center">
                <div class="tab-content"><label class="form-label text-center">Transcript Image 1</label>
                      <div><a href="{{ $professional->pcl->full_transcript_bac_1 }}"
                            data-fancybox="image"><img src="{{ $professional->pcl->full_transcript_bac_1 }}"></a>
                    </div>
                </div>
            </div>
            @endif
            @if ($professional->pcl->transcript_bac_2)
            <div class="col-md-4 mb-3 text-center">
                <div class="tab-content"><label class="form-label text-center">Transcript Image 2</label>
                      <div><a href="{{ $professional->pcl->full_transcript_bac_2 }}"
                            data-fancybox="image"><img src="{{ $professional->pcl->full_transcript_bac_2 }}"></a>
                    </div>
                </div>
            </div>
            @endif
            @if ($professional->pcl->transcript_bac_3)
            <div class="col-md-4 mb-3 text-center">
                <div class="tab-content"> <label class="form-label text-center">Transcript Image 3</label>
                      <div><a href="{{ $professional->pcl->full_transcript_bac_3 }}"
                            data-fancybox="image"><img src="{{ $professional->pcl->full_transcript_bac_3 }}"></a>
                    </div>
                </div>
            </div>
            @endif
            @if ($professional->pcl->provisional_image)
            <div class="col-md-4 mb-3 text-center">
                <div class="tab-content"> <label class="form-label text-center">Provisional Image</label>
                    <div class="mb-3"><a href="{{ $professional->pcl->full_provisional_image }}"
                            data-fancybox="image"><img src="{{ $professional->pcl->full_provisional_image }}"></a>

                    </div>
                </div>
            </div>
            @endif
            @if ($professional->pcl->character_image)
            <div class="col-md-4 mb-3 text-center">
                <div class="tab-content"> <label class="form-label text-center">Character Image</label>
                      <div><a href="{{ $professional->pcl->full_character_image }}"
                            data-fancybox="image"><img src="{{ $professional->pcl->full_character_image }}"></a>
                    </div>
                </div>
            </div>
            @endif
            @if ($professional->pcl->equivalence_certificate)
            <div class="col-md-4 mb-3 text-center">
                <div class="tab-content"> <label class="form-label text-center">Equivalence Image</label>
                      <div><a href="{{ $professional->pcl->full_equivalence_certificate }}"
                            data-fancybox="image"><img src="{{ $professional->pcl->full_equivalence_certificate }}"></a>
                    </div>
                </div>
            </div>
            @endif
            @if ($professional->pcl->ojt_image)
            <div class="col-md-4 mb-3 text-center">
                <div class="tab-content"> <label class="form-label text-center">OJT Image</label>
                    <div class="mb-3"><a href="{{ $professional->pcl->full_ojt_image }}" data-fancybox="image"><img
                                src="{{ $professional->pcl->full_ojt_image }}"></a>
                    </div>
                </div>
            </div>
            @endif
            @if ($professional->pcl->noc_image)
            <div class="col-md-4 mb-3 text-center">
                <div class="tab-content"> <label class="form-label text-center">NOC Image</label>
                    <div class="mb-3"><a href="{{ $professional->pcl->full_noc_image }}" data-fancybox="image"><img
                                src="{{ $professional->pcl->full_noc_image }}"></a>
                    </div>
                </div>
            </div>
            @endif
            @if ($professional->pcl->intership_image)
            <div class="col-md-4 mb-3 text-center">
                <div class="tab-content"> <label class="form-label text-center">Internship Image</label>
                    <div class="mb-3"><a href="{{ $professional->pcl->full_intership_image }}"
                            data-fancybox="image"><img src="{{ $professional->pcl->full_intership_image }}"></a>
                    </div>
                </div>
            </div>
            @endif
            @if ($professional->pcl->council_registration_certificate)
            <div class="col-md-4 mb-3 text-center">
                <div class="tab-content"> <label class="form-label text-center">Council Registration Certificate</label>
                    <div class="mb-3"><a href="{{ $professional->pcl->full_council_registration_certificate }}"
                            data-fancybox="image"><img
                                src="{{ $professional->pcl->full_council_registration_certificate }}"></a>
                    </div>
                </div>
            </div>
            @endif
            @if ($professional->pcl->ojt_pcl_community_1_image)
            <div class="col-md-4 mb-3 text-center">
                <div class="tab-content"> <label class="form-label text-center">Clinical / Community Work Image
                        1</label>
                    <div class="mb-3"><a href="{{ $professional->pcl->full_ojt_pcl_community_1_image }}"
                            data-fancybox="image"><img
                                src="{{ $professional->pcl->full_ojt_pcl_community_1_image }}"></a>
                    </div>
                </div>
            </div>
            @endif
            @if ($professional->pcl->ojt_pcl_community_2_image)
            <div class="col-md-4 mb-3 text-center">
                <div class="tab-content"> <label class="form-label text-center">Clinical / Community Work Image
                        2</label>
                    <div class="mb-3"><a href="{{ $professional->pcl->full_ojt_pcl_community_2_image }}"
                            data-fancybox="image"><img
                                src="{{ $professional->pcl->full_ojt_pcl_community_2_image }}"></a>
                    </div>
                </div>
            </div>
            @endif
            @if ($professional->pcl->passport_image)
            <div class="col-md-4 mb-3 text-center">
                <div class="tab-content"> <label class="form-label text-center">Passport Image</label>
                    <div class="mb-3"><a href="{{ $professional->pcl->full_passport_image }}" data-fancybox="image"><img
                                src="{{ $professional->pcl->full_passport_image }}"></a>
                    </div>
                </div>
            </div>
            @endif
            @if ($professional->pcl->visa_image)
            <div class="col-md-4 mb-3 text-center">
                <div class="tab-content"> <label class="form-label text-center">VISA Image</label>
                    <div class="mb-3"><a href="{{ $professional->pcl->full_visa_image }}" data-fancybox="image"><img
                                src="{{ $professional->pcl->full_visa_image }}"></a>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>