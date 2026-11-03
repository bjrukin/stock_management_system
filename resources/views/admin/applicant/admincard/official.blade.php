<div class="container mt-5 mb-5 text-center">
    <div style="width: 90%; margin: 0 auto; border: 1px dashed; text-align: center;"></div>
</div>
<div class="container">
    @include("admin.applicant.admincard.header")
    <div class="mb-1  px-3" style="font-size: 16px;">
        <div class="row">
            <div class="col-md-6">कार्यालय प्रयोजनको लागि</div>
            <div class="col-md-6">
                <div class="float-end">Website: www.nhpc.gov.np</div>
            </div>
        </div>
    </div>
    <div style="border-style:solid; border-width:1px; padding:5px;">
        <div class="row">
            <div class="col-md-6">
                <div style="font-size: 14px;" class="mt-3 ps-3">
                    <p>रोल नम्बर : <strong style="font-size:18px">
                            @if ($applicant->admit_card)
                            {{ $applicant->admit_card->symbol_number }}
                            @else
                            Symbol Number Not generated yet
                            @endif
                        </strong>
                    </p>
                    <p>पर्रीक्षार्थीको नाम : <strong><span
                                style="font-size:20px;">{{ $applicant->professional->info->full_name }}</span></strong>
                    </p>
                    <p>जन्ममिती : {{ $applicant->professional->info->dob_nep }} ( AD :
                        {{ $applicant->professional->info->dob_eng }} )
                    </p>
                    <p>फोन नं. :
                        {{ $applicant->professional->phone }}</p>
                    <p>विषयको नाम : <strong style="font-size:18px;">
                            {{ $applicant->program->name }} - तह : {{ $applicant->level->short_name_english }}
                        </strong>
                    </p>
                    <p>परीक्षा केन्द्र : Advance Education & Innovative Research Center (AEIRC), Babarmahal, Kathmandu
                    </p>
                    <div>परीक्षार्थीको हस्ताक्षर : <br>
                        <div style="border-width: 1px; height: 120px; width:300px; border-style: dashed;" class="mb-3">
                            <img style="height: 100% ;width: 100%; object-fit:contain;object-position:center;"
                                src="{{ $applicant->professional_info?->full_signature_image }}" alt="">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="float-end mt-3 pe-3">
                    <img src="{{ $applicant->professional->info->full_profile_picture }}"
                        style="width:180px; height:180px;">
                    <div style="margin-top: -55px; margin-left:-20px; z-index: 99;position: absolute; height: 50px">
                        <img src="{{ asset('assets/images/signature_sachip.png') }}" style="width:180px; height: 85px;">
                    </div>
                    <div> ..................................................<br>
                        सदस्य सचिवको हस्ताक्षर
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>