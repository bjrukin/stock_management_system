@php
use App\Models\Applicant;
use Illuminate\Support\Facades\Auth;

$professional_id = Auth::guard('professional')->id();

$exam = Applicant::where('professional_id', $professional_id)
->whereHas('exam', function ($qry) {
$qry->where('status', 1);
})
->orderBy('id', 'DESC')
->first();

$passed_exam = Applicant::where('professional_id', $professional_id)
->where('is_passed', 1)
->first();
$show_profile = (!$exam || ($exam && $exam->status == 'rejected')) && !$passed_exam;
@endphp

<div class="left-side-menu mt-3">
    <div class="h-100" data-simplebar>

        <div id="sidebar-menu">
            <ul id="side-menu">
                <li class="@if(isset($nav) && $nav == 'dashboard') menuitem-active @endif"> <a
                        href="{{ route('professional.dashboard') }}">
                        <i class="mdi mdi-view-dashboard-outline"></i> <span>Dashboard</span></a>
                </li>
                <li class="@if(isset($nav) && $nav == 'account') menuitem-active @endif"> <a
                        href="{{ route('professional.account') }}"
                        class="@if(isset($nav) && $nav == 'account') active @endif">
                        <i class="mdi mdi-account-check-outline"></i> <span>My Account</span></a>
                </li>
                @if($show_profile)
                <li class="@if(isset($nav) && $nav == 'profile') menuitem-active @endif"> <a
                        href="{{ route('professional.profile.personal') }}"
                        class="@if(isset($nav) && $nav == 'profile') active @endif">
                        <i class="mdi mdi-view-dashboard-outline"></i> <span> My Profile </span></a>
                </li>
                @endif
                <li class="@if(isset($nav) && $nav == 'exam') menuitem-active @endif"> <a
                        href="{{ route('professional.exam.dashboard') }}"
                        class="@if(isset($nav) && $nav == 'exam') active @endif">
                        <i class="mdi mdi-view-dashboard-outline"></i> <span> Apply Exam </span></a>
                </li>
                <li class="@if(isset($nav) && $nav == 'tslc') menuitem-active @endif"> <a
                        href="{{ route('professional.exam.tslc') }}"
                        class="@if(isset($nav) && $nav == 'tslc') active @endif">
                        <i class="mdi mdi-view-dashboard-outline"></i> <span> Apply TSLC </span></a>
                </li>
                <li class="@if($nav == 'certificate') menuitem-active @endif">
                    <a href="#certificate" data-bs-toggle="collapse"> <i class="mdi mdi-view-dashboard-outline"></i>
                        <span>Certificate</span> <span class="menu-arrow"></span> </a>
                    <div class="collapse  @if($nav == 'rolepermission') show @endif" id="certificate">
                        <ul class="nav-second-level">
                            <li class="@if($sub_nav == 'list') menuitem-active @endif"> <a
                                    href="{{route('professional.certificate')}}"
                                    class="@if($sub_nav == 'list') active @endif">Certificate List</a> </li>
                            <li class="@if($sub_nav == 'verificationstep') menuitem-active @endif"> <a
                                    href="{{route('professional.exam.tslc')}}"
                                    class="@if($sub_nav == 'verificationstep') active @endif">
                                    <span>New Certificate Request </span> </a>
                            </li>
                            <li class="@if($sub_nav == 'verificationstep') menuitem-active @endif"> <a
                                    href="{{route('professional.exam.tslc')}}"
                                    class="@if($sub_nav == 'verificationstep') active @endif">
                                    <span>Renew Certificate </span> </a>
                            </li>
                            <li class="@if($sub_nav == 'verificationstep') menuitem-active @endif"> <a
                                    href="{{route('professional.exam.tslc')}}"
                                    class="@if($sub_nav == 'verificationstep') active @endif">
                                    <span>Verify Certificate And Good Standing Letter </span> </a>
                            </li>


                        </ul>
                    </div>
                </li>

                <li class="@if(isset($nav) && $nav == 'kyc') menuitem-active @endif"> <a
                        href="{{ route('professional.kyc') }}" class="@if(isset($nav) && $nav == 'kyc') active @endif">
                        <i class="mdi mdi-view-dashboard-outline"></i> <span>KYC</span></a>
                </li>
            </ul>
        </div>
        <div class="clearfix"></div>
    </div>
</div>