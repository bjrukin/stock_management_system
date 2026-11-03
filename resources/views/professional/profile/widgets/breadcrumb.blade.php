@php
use App\Models\Applicant;
use Illuminate\Support\Facades\Auth;
$exam = Applicant::where('professional_id', Auth::Guard('professional')->id())->whereHas('exam', function($qry) {
$qry->where('status', 1);})->orderBy('id', 'DESC')->first();
@endphp
<div class="card">
    <div class="card-body">
        <ul class="nav nav-tabs nav-bordered nav-justified">
            <li class="nav-item">
                <a href="{{ route('professional.profile.personal')}}" class="nav-link @if($sub_nav == 'personal') active @endif">
                    Personal
                </a>
            </li>
            <li class="nav-item">
            @if($sub_nav == 'personal')
            <a class="nav-link @if($sub_nav == 'guardian') active @endif">
                    Guardian
                </a>
            @else
                <a href="{{ route('professional.profile.guardian')}}" class="nav-link @if($sub_nav == 'guardian') active @endif">
                    Guardian
                </a>
            @endif
            </li>
            <li class="nav-item">
            @if($sub_nav == 'personal' || $sub_nav == 'guardian')
            <a class="nav-link @if($sub_nav == 'college') active @endif">
            College
                </a>
            @else
                <a href="{{ route('professional.profile.slc')}}" class="nav-link @if($sub_nav == 'college') active @endif">
                    College
                </a>
            @endif
            </li>
            @if($exam && $exam->status == 'rejected')
            <li class="nav-item">
                <a class="nav-link  @if($sub_nav == 'voucher')active @endif">
                    Voucher
                </a>
            </li>
            @endif
        </ul>
    </div>
</div>
