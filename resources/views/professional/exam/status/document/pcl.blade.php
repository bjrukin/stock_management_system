<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="row">
            <div class="col-md-12 info-container mb-3">
                <div><strong>College Name: </strong><span>{{ $professional->pcl->college_name }}</span></div>
                <div><strong>Board: </strong><span>{{ $professional->pcl->board_university }}</span></div>
                <div><strong>Passed Year: </strong><span>{{ $professional->pcl->passed_year }}</span></div>
                <div><strong>Registration Number: </strong><span>{{ $professional->pcl->registration_number }}</span></div>
            </div>
            @if($professional->pcl->transcript_image)
                <div class="col-md-4 img-container">
                    <strong>Transcript Image</strong>
                    <div class="document-image">
                        <a href="{{ $professional->pcl->full_transcript_image }}" data-fancybox="image">
                            <img src="{{ $professional->pcl->full_transcript_image }}">
                        </a>
                    </div>
                </div>
            @endif
            @if($professional->pcl->transcript_bac_1)
                <div class="col-md-4 img-container">
                    <strong>Transcript Image</strong>
                    <div class="document-image">
                        <a href="{{ $professional->pcl->full_transcript_bac_1 }}" data-fancybox="image">
                            <img src="{{ $professional->pcl->full_transcript_bac_1 }}">
                        </a>
                    </div>
                </div>
            @endif
            @if($professional->pcl->provisional_image)
                <div class="col-md-4 img-container">
                    <strong>Provisional Image</strong>
                    <div class="document-image">
                        <a href="{{ $professional->pcl->full_provisional_image }}" data-fancybox="image">
                            <img src="{{ $professional->pcl->full_provisional_image }}">
                        </a>
                    </div>
                </div>
            @endif
            @if($professional->pcl->character_image)
                <div class="col-md-4 img-container">
                    <strong>Character Image</strong>
                    <div class="document-image">
                        <a href="{{ $professional->pcl->full_character_image }}" data-fancybox="image">
                            <img src="{{ $professional->pcl->full_character_image }}">
                        </a>
                    </div>
                </div>
            @endif
            @if($professional->pcl->equivalence_certificate)
                <div class="col-md-4 img-container">
                    <strong>Equivalence Image</strong>
                    <div class="document-image">
                        <a href="{{ $professional->pcl->full_equivalence_certificate }}" data-fancybox="image">
                            <img src="{{ $professional->pcl->full_equivalence_certificate }}">
                        </a>
                    </div>
                </div>
            @endif
            @if($professional->pcl->ojt_image)
                <div class="col-md-4 img-container">
                    <strong>OJT Image</strong>
                    <div class="document-image">
                        <a href="{{ $professional->pcl->full_ojt_image }}" data-fancybox="image">
                            <img src="{{ $professional->pcl->full_ojt_image }}">
                        </a>
                    </div>
                </div>
            @endif
            @if($professional->pcl->noc_image)
                <div class="col-md-4 img-container">
                    <strong>NOC Image</strong>
                    <div class="document-image">
                        <a href="{{ $professional->pcl->full_noc_image }}" data-fancybox="image">
                            <img src="{{ $professional->pcl->full_noc_image }}">
                        </a>
                    </div>
                </div>
            @endif
            @if($professional->pcl->intership_image)
                <div class="col-md-4 img-container">
                    <strong>Internship Image</strong>
                    <div class="document-image">
                        <a href="{{ $professional->pcl->full_intership_image }}" data-fancybox="image">
                            <img src="{{ $professional->pcl->full_intership_image }}">
                        </a>
                    </div>
                </div>
            @endif
            @if($professional->pcl->council_registration_certificate)
                <div class="col-md-4 img-container">
                    <strong>Council Registration Certificate</strong>
                    <div class="document-image">
                        <a href="{{ $professional->pcl->full_council_registration_certificate }}" data-fancybox="image">
                            <img src="{{ $professional->pcl->full_council_registration_certificate }}">
                        </a>
                    </div>
                </div>
            @endif
            @if($professional->pcl->ojt_pcl_community_1_image)
                <div class="col-md-4 img-container">
                    <strong>Clinical / Community Work Image 1</strong>
                    <div class="document-image">
                        <a href="{{ $professional->pcl->full_ojt_pcl_community_1_image }}" data-fancybox="image">
                            <img src="{{ $professional->pcl->full_ojt_pcl_community_1_image }}">
                        </a>
                    </div>
                </div>
            @endif
            @if($professional->pcl->ojt_pcl_community_2_image)
                <div class="col-md-4 img-container">
                    <strong>Clinical / Community Work Image 2</strong>
                    <div class="document-image">
                        <a href="{{ $professional->pcl->full_ojt_pcl_community_2_image }}" data-fancybox="image">
                            <img src="{{ $professional->pcl->full_ojt_pcl_community_2_image }}">
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>