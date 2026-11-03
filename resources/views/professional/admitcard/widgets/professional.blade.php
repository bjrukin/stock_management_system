<div class="container mt-3">
    @include("admin.applicant.admincard.header")
    <div class="mt-3" style="border: 1px solid #000; padding: 20px; height:auto;">
        <div class="row">
            <div class="col-md-7">
                <p>रोल नम्बर : <strong style="font-size:18px">
                        @if ($applicant->admit_card)
                        {{ $applicant->admit_card->symbol_number }}
                        @else
                        Symbol Number Not generated yet
                        @endif
                    </strong>
                </p>
                <p>पर्रीक्षार्थीको नाम : <strong><span
                            style="font-size:20px; text-transform:capitalize;">{{ $applicant->professional->info->full_name }}</span></strong>
                </p>
                लिङ्ग <span style="text-transform:capitalize;">:
                    {{ $applicant->professional->info->gender }}<p>
                    </p>
                    <p>जन्ममिती :
                        {{ $applicant->professional->info->dob_nep }} (
                        AD :
                        {{ $applicant->professional->info->dob_eng }} ) </p>
                    <p>फोन नं. :
                        {{ $applicant->professional->phone }}
                    </p>
                    <p>विषयको नाम <strong style="font-size:18px;">:
                            {{ $applicant->program->name }} - तह :
                            {{ $applicant->level->short_name_english }}
                        </strong>
                    </p>
                    <p>परीक्षा केन्द्र : <span>Advance Education & Innovative Research Center (AEIRC)
                            <br><span class="ps-5">Babarmahal, Kathmandu</span></span>
                    </p>

                    <div style="float:left;">परीक्षार्थीको हस्ताक्षर : <br>
                        <div style="border-width: 1px; height: 120px; width:300px; border-style: dashed;">
                            <img style="height: 100%;width: 100%;object-fit:contain;object-position:center;"
                                src="{{ $applicant->professional_info?->full_signature_image }}" alt="">
                        </div>
                    </div>
                </span>
            </div>
            <div class="col-md-5">
                <div class="mt-3 pe-3">
                    <div style="display:flex;">
                        <img src="{{ $applicant->professional->info->full_profile_picture }}" style="width:180px; height:180px;">
                        <div
                            style="border-style:dashed; border-width: 1px;height: 180px; width: 170px; object-position: 50% 100%; padding:10px; line-height:1.5">
                            <p style="width:155px">परीक्षाको लागि फाराम भर्दाको पासपोर्ट
                                साइज को फोटो टास गरी हस्ताक्षर गर्नुपर्नेछ।
                            </p>
                        </div>
                    </div>
                    <div>
                        <div style="margin-top: -45px; margin-left:-20px; z-index: 99;position: absolute; height: 50px">
                            <img src="{{ asset('assets/images/signature_sachip.png') }}"
                                style="width:180px; height: 85px;">
                        </div>
                        <div style="padding-left:5px;">
                            ...............................................<br>
                            सदस्य सचिवको हस्ताक्षर
                        </div>
                    </div>
                    <div class="mt-5">
                        <table
                            style="border: 1px solid black; border-collapse: collapse; text-align:center; overflow: auto;  font-size: 12px; ">
                            <tbody>
                                <tr>
                                    <td colspan="2" style="text-align: center; font-size: 12px;  padding:3px;">
                                        औठा छाप</td>
                                </tr>
                                <tr>
                                    <td
                                        style="border: 1px solid black; border-collapse: collapse; text-align:center; font-size: 12px;  padding:3px;">
                                        दाँया</td>
                                    <td
                                        style="border: 1px solid black; border-collapse: collapse; text-align:center; font-size: 12px;  padding:3px;">
                                        बायाँ</td>
                                </tr>
                                <tr>
                                    <td
                                        style="height: 140px; width:140px;border: 1px solid black; border-collapse: collapse; text-align:center">
                                    </td>
                                    <td
                                        style="height: 140px; width:140px;border: 1px solid black; border-collapse: collapse; text-align:center">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>