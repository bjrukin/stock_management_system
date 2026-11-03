@extends('layout.professional')
@section('content')
    <div class="content" id="professional.dashboard">
        <div class="container-fluid pb-5">
            <div class="card">
                <div class="card-body">
                    <h4 class="page-title-main float-start">Dashboard</h4>
                </div>
            </div>
            <div class="row">
                @if ($lastest_apply ) 
                <!-- ($lastest_apply && $lastest_apply->is_admit_card_generate) -->
                    <div class="col-12 mt-3">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-light border-bottom">
                                <strong class="py-1 d-block">Congratulations !!</strong>
                            </div>
                            <div class="card-body">
                                <strong>Your document has been successfully reviewed and approved. You can download your admit
                                    card by clicking the button below.</strong><br>
                                <strong>तपाईँको कागजात सफलतापूर्वक समीक्षा गरियो र स्वीकृत भयो। तपाईँले आफ्नो प्रवेश पत्र
                                    डाउनलोड गर्न "Admit Card" बटनमा क्लिक गर्न सक्नुहुन्छ।</strong><br>
                                <a class="btn btn-outline-danger mt-2" href="{{ route('professional.admitcard') }}">
                                    <i class="mdi mdi-view-dashboard-outline"></i><span> Admit Card </span> </a>
                            </div>
                        </div>
                    </div>
                @endif

                @if ($lastest_apply && strtolower($lastest_apply->status) == 'rejected')
                    <div class="col-xl-12 col-md-12 mt-3">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-light border-bottom">
                                <strong class="py-1 d-block">Warning</strong>
                            </div>
                            <div class="card-body">
                                <div class="widget-chart-1">
                                    <div class="widget-chart-box-1 float-start">
                                        <p class="mb-2"
                                            style="font-size: 15px; font-weight: 500; line-height: 1.6; color: #495057;">
                                            Your file has been rejected from <b
                                                class="text-capitalize">{{ $lastest_apply->state }}</b> for student
                                            review. Please review and update your documents. </p>
                                        <p class="mb-3"
                                            style="font-size: 15px; font-weight: 500; line-height: 1.6; color: #495057;">
                                            Steps: Go to profile and update press the submit button</p>

                                        <p class="mb-2"
                                            style="font-size: 15px; font-weight: 500; line-height: 1.6; color: #495057;">
                                            तपाइँको फाइल विद्यार्थी समीक्षाको लागि <b
                                                class="text-capitalize">{{ $lastest_apply->state }}</b>बाट अस्वीकार गरिएको छ।
                                            कृपया
                                            आफ्नो कागजातहरू समीक्षा र अद्यावधिक गर्नुहोस्। </p>
                                        <p class="mb-0"
                                            style="font-size: 15px; font-weight: 500; line-height: 1.6; color: #495057;">
                                            चरणहरू: प्रोफाइलमा जानुहोस् र सबमिट बटन थिच्नुहोस् अपडेट गर्नुहोस्</p>
                                    </div>
                                    <div class="widget-detail-1">
                                        <a href="{{ route('professional.profile.personal') }}" class="btn btn-primary">Profile
                                            Update</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="row mt-3">
                @if ($lastest_apply)
                    <div class="col-lg-12">
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header">
                                <strong>Notice</strong>
                            </div>
                            <div class="card-body">
                                <p class="mb-2" style="font-size: 15px; font-weight: 500; line-height: 1.6; color: #495057;">
                                    You have successfully applied for {{ optional($lastest_apply->exam)->name ?? 'Exam' }} Liscence Exam
                                    ({{ $lastest_apply->created_at->format('Y-m-d') }}).
                                    @if ($lastest_apply->is_admit_card_generate)
                                        Your documents have been reviewed and approved.
                                    @else
                                        Your documents are under review.
                                    @endif
                                </p>
                                <p class="mb-0" style="font-size: 15px; font-weight: 500; line-height: 1.6; color: #495057;">
                                    {{ optional($lastest_apply->exam)->name_nep ?? 'परीक्षा' }} लाइसेन्स परीक्षाको लागि सफलतापूर्वक आवेदन दिनुभएको छ। 
                                    ({{ $lastest_apply->created_at->format('Y-m-d') }})
                                    @if ($lastest_apply->is_admit_card_generate)
                                        तपाईंका कागजातहरू समीक्षा भई स्वीकृत भएका छन्।
                                    @else
                                        तपाईंका कागजातहरू समीक्षा अन्तर्गत छन्।
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header">
                                <strong>Document Status</strong>
                            </div>
                            <div class="card-body">
                                <div class="timeline-wrapper">
                                    <div class="timeline-line"></div>
                                    <div class="timeline-items">
                                        @php
                                            $states = ['operator', 'officer', 'registrar', 'subject_committee', 'exam_committee', 'council'];
                                            $current_found = false;
                                        @endphp
                                        @foreach($states as $state)
                                            @php
                                                $state = strtolower(str_replace(' ', '_', $state));
                                                $log = $applicant_logs->where('state', $state)->last();
                                                $is_current = (strtolower($lastest_apply->state) == $state);
                                                $status = $is_current ? strtolower($lastest_apply->status) : ($current_found ? '--' : 'accepted');
                                            @endphp
                                            <div class="timeline-item {{ $is_current ? 'active' : '' }}">
                                                <div class="timeline-circle">
                                                    @if($status == 'accepted')
                                                        <i class="bi bi-check-lg" style="font-size: 20px;"></i>
                                                    @endif
                                                </div>
                                                <div class="timeline-box status-{{ $status }}">
                                                    <div class="box-title">{{ ucwords(str_replace('_', ' ', $state)) }}</div>
                                                    <div class="box-status">- {{ ucwords($status) }} -</div>
                                                    <div class="box-divider"></div>
                                                    <div class="box-details">
                                                        @if ($is_current && !$log && $state == 'operator' && $status == 'progress')
                                                            Under Review
                                                        @elseif ($log)
                                                            {{ $log->remarks }}
                                                        @elseif ($is_current)
                                                            {{ $lastest_apply->remarks ?? '--' }}
                                                        @elseif($status == 'accepted')
                                                            Document Verified
                                                        @else
                                                            --
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            @if($is_current) @php $current_found = true; @endphp @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="col-12 mt-3">
                        <div class="card border-warning border shadow-sm mb-3">
                            <div class="card-header bg-light">
                                <strong>No Exam Applied Yet</strong>
                            </div>
                            <div class="card-body">
                                <p>You have not applied for any exam yet. Click the button below to apply for an exam now.</p>
                                <p>तपाईंले अहिलेसम्म कुनै परीक्षामा आवेदन दिनुभएको छैन। तलको बटनमा क्लिक गरेर आवेदन दिनुहोस्।
                                </p>
                                <a href="{{ route('professional.exam.dashboard') }}" class="btn btn-primary">
                                    Apply for Exam / परीक्षाको लागि आवेदन दिनुहोस्
                                </a>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            <div class="row mb-5 pb-5">
                <div class="col-lg-12 col-md-12">
                    <div class="card border-info mb-3">
                        <div class="card-header">
                            <strong>Certificate Renewal / प्रमाणपत्र नवीकरण</strong>
                        </div>
                        <div class="card-body">
                            <p>Do you want to renew your certificate? <br> के तपाईं आफ्नो प्रमाणपत्र नवीकरण गर्न चाहनुहुन्छ?
                            </p>
                            <a href="#" class="btn btn-primary">
                                Renew Certificate / प्रमाणपत्र नवीकरण गर्नुहोस्
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('footer-scripts')
    @include('professional.dashboard.style.index')
@endpush