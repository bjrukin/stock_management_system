 <div class="user-profile-box d-flex flex-column align-items-center">
     @if($lastest_applicant)
     <h3 class="text-capitalize mb-2">State : {{ ucwords(strtolower(str_replace('_', ' ', $lastest_applicant->state ))) }}</h3>
     @endif
     @if( $professional->info)
     <div class="image-preview">
         <a href="{{ $professional->info->full_profile_picture }}" data-fancybox="image"
             title="{{ $professional->info->full_name }}"> <img style="height:250px; width:100% ; object-fit:contain;"
                 src="{{ $professional->info->full_profile_picture }}"></a>
     </div>
     @endif
     <div class="profile-detail d-flex flex-column align-items-center">
         <div class="mt-2">
             <h3>{{ $professional->name }}
             </h3>
         </div>
         <div class="mt-1">
             {{ $professional->email }}
         </div>
         <div class="mt-1">
             {{ $professional->phone }}
         </div>
     </div>
 </div>