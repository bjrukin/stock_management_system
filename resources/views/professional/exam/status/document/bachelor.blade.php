 <div class="row">
     <div class="col-md-12 col-sm-12">
         <div class="row">
             <div class="col-md-12 info-container mb-3">
                 <div><strong>College Name :
                     </strong><span>{{$professional->bachelor->college_name}}</span></div>
                 <div><strong>University :
                     </strong><span>{{$professional->bachelor->board_university}}</span>
                 </div>
                 <div><strong>Admission Year :
                     </strong><span>{{$professional->bachelor->admission_year}}</span></div>
                 <div><strong>Passed Year :
                     </strong><span>{{$professional->bachelor->passed_year}}</span></div>
                 <div><strong>Registration Number :
                     </strong><span>{{$professional->bachelor->registration_number}}</span>
                 </div>
             </div>
             @if($professional->bachelor->transcript_bac_1)
             <div class="col-md-4 img-container">
                 <strong>Transcript Image 1</strong>
                 <div class="document-image"><a href="{{ $professional->bachelor->full_transcript_bac_1 }}"
                         data-fancybox="image"><img src="{{ $professional->bachelor->full_transcript_bac_1 }}"></a>
                 </div>
             </div>
             @endif
             @if($professional->bachelor->transcript_bac_2)
             <div class="col-md-4 img-container">
                 <strong>Transcript Image 2</strong>
                 <div class="document-image"> <a href="{{ $professional->bachelor->full_transcript_bac_2 }}"
                         data-fancybox="image"><img src="{{ $professional->bachelor->full_transcript_bac_2 }}"></a>
                 </div>
             </div>
             @endif
             @if($professional->bachelor->transcript_bac_3)
             <div class="col-md-4 img-container">
                 <strong>Transcript Image 3</strong>
                 <div class="document-image"> <a href="{{ $professional->bachelor->full_transcript_bac_3 }}"
                         data-fancybox="image"><img src="{{ $professional->bachelor->full_transcript_bac_3 }}"></a>
                 </div>
             </div>
             @endif
             @if($professional->bachelor->transcript_bac_4)
             <div class="col-md-4 img-container">
                 <strong>Transcript Image 4</strong>
                 <div class="document-image"> <a href="{{ $professional->bachelor->full_transcript_bac_4 }}"
                         data-fancybox="image"><img src="{{ $professional->bachelor->full_transcript_bac_4 }}"></a>
                 </div>
             </div>
             @endif
             @if($professional->bachelor->transcript_bac_5)
             <div class="col-md-4 img-container">
                 <strong>Transcript Image 5</strong>
                 <div class="document-image"> <a href="{{ $professional->bachelor->full_transcript_bac_5 }}"
                         data-fancybox="image"><img src="{{ $professional->bachelor->full_transcript_bac_5 }}"></a>
                 </div>
             </div>
             @endif
             @if($professional->bachelor->transcript_bac_6)
             <div class="col-md-4 img-container">
                 <strong>Transcript Image 6</strong>
                 <div class="document-image"> <a href="{{ $professional->bachelor->full_transcript_bac_6 }}"
                         data-fancybox="image"><img src="{{ $professional->bachelor->full_transcript_bac_6 }}"></a>
                 </div>
             </div>
             @endif
             @if($professional->bachelor->transcript_bac_7)
             <div class="col-md-4 img-container">
                 <strong>Transcript Image 7</strong>
                 <div class="document-image"> <a href="{{ $professional->bachelor->full_transcript_bac_7 }}"
                         data-fancybox="image"><img src="{{ $professional->bachelor->full_transcript_bac_7 }}"></a>
                 </div>
             </div>
             @endif
             @if($professional->bachelor->transcript_bac_8)
             <div class="col-md-4 img-container">
                 <strong>Transcript Image 8</strong>
                 <div class="document-image"> <a href="{{ $professional->bachelor->full_transcript_bac_8 }}"
                         data-fancybox="image"><img src="{{ $professional->bachelor->full_transcript_bac_8 }}"></a>
                 </div>
             </div>
             @endif
             @if($professional->bachelor->equivalence_certificate)
             <div class="col-md-4 img-container">
                 <strong>Equivalence Certificate</strong>
                 <div class="document-image"> <a href="{{ $professional->bachelor->full_equivalence_certificate }}"
                         data-fancybox="image"><img
                             src="{{ $professional->bachelor->full_equivalence_certificate }}"></a>
                 </div>
             </div>
             @endif
             <div class="col-md-4 img-container">
                 <strong>Provisional Image</strong>
                 <div class="document-image">
                     @if($professional->bachelor->provisional_image) <a
                         href="{{ $professional->bachelor->full_provisional_image }}" data-fancybox="image"><img
                             src="{{ $professional->bachelor->full_provisional_image }}"
                             id="display_provisional_image"></a> @else <img src="" id="display_provisional_image"
                         class="d-none preview-image">
                     @endif
                 </div>
             </div>
             @if($professional->bachelor->character_image)
             <div class="col-md-4 img-container">
                 <strong>Character Image</strong>
                 <div class="document-image"><a href="{{ $professional->bachelor->full_character_image }}"
                         data-fancybox="image"><img src="{{ $professional->bachelor->full_character_image }}"></a>
                 </div>
             </div>
             @endif
             @if($professional->bachelor->intership_image)
             <div class="col-md-4 img-container">
                 <strong>Internship Image</strong>
                 <div class="document-image"> <a href="{{ $professional->bachelor->full_intership_image }}"
                         data-fancybox="image"><img src="{{ $professional->bachelor->full_intership_image }}"></a>
                 </div>
             </div>
             @endif
             @if($professional->bachelor->passport_image)
             <div class="col-md-4 img-container">
                 <strong>NHPC permission Letter Image</strong>
                 <div class="document-image"><a href="{{ $professional->bachelor->full_noc_image }}"
                         data-fancybox="image"><img src="{{ $professional->bachelor->full_noc_image }}"></a>
                 </div>
             </div>
             @endif
             @if($professional->bachelor->visa_image)
             <div class="col-md-4 img-container">
                 <strong>VISA Image</strong>
                 <div class="document-image"> <a href="{{ $professional->bachelor->full_visa_image }}"
                         data-fancybox="image"><img src="{{ $professional->bachelor->full_visa_image }}"></a>
                 </div>
             </div>
             @endif
             @if($professional->bachelor->passport_image)
             <div class="col-md-4 img-container">
                 <strong>Passport Image</strong>
                 <div class="document-image"> <a href="{{ $professional->bachelor->full_passport_image }}"
                         data-fancybox="image"><img src="{{ $professional->bachelor->full_passport_image }}"></a>
                 </div>
             </div>
             @endif

         </div>
     </div>
 </div>
