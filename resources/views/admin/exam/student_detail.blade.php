@extends('layout.admin')
@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-xl-12 col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <h3>{{ $exam->name }}</h3>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div>
                                <div class="title float-start">
                                    <h3>Student Detail</h3>
                                </div>
                                <div class="float-end">
                                    {{-- <a href="{{route('exam_committee-applicant-export', ['exam_id' => $exam->id])}}" class="btn btn-primary">Download</a> --}}
                                </div>
                                <div class="clearfix"></div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="card bordered">
                                            <div class="card-body">
                                                <h2 class="card-title">{{ $applicant_count }}</h2>
                                                <h3 class="card-subtitle mb-2 text-muted">Total </h3>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card bordered">
                                            <div class="card-body">
                                                <h2 class="card-title">{{ $admit_card_count }}</h2>
                                                <h3 class="card-subtitle mb-2 text-muted">Appeared in Exam </h3>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card bordered">
                                            <div class="card-body">
                                                <h2 class="card-title">{{ $pass_count }}</h2>
                                                <h3 class="card-subtitle mb-2 text-muted">Pass</h3>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card bordered">
                                            <div class="card-body">
                                                <h2 class="card-title"> @if ($exam->result_published){{ $failed_count }}@else 0 @endif</h2>
                                                <h3 class="card-subtitle mb-2 text-muted">Fail</h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="dropdown float-end">
                        {{-- <a href="{{ route('exam_committee-routine-generate') }}" class="btn btn-primary">View Routine</a> --}}
                        {{-- <a href="{{ route('exam_committee-result-form') }}" class="btn btn-primary">Upload Result</a> --}}
                        <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="mdi mdi-dots-vertical"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <!-- item-->
                            <a href="javascript:void(0);" class="dropdown-item">Download Admit Card Detail</a>
                            <!-- item-->
                            <a href="javascript:void(0);" class="dropdown-item">Download Student Detail</a>
                            <a href="{{ route('admin.admitcard.addEdit', ['id'=>$exam->id]) }}" class="dropdown-item" data-exam_id="{{ $exam->id }}">
                                Generate Admit Card
                            </a>
                            <a href="{{ route('admin.exam.routine', ['id'=>$exam->id]) }}" class="dropdown-item" data-exam_id="{{ $exam->id }}">
                                Generate Routine
                            </a>
                        </div>
                    </div>
                    <h4 class="header-title mt-0 mb-3">Program Wise Detail</h4>

                    <div class="table-responsive">
                        @php 
                            $sn = 1;
                        @endphp
                        @foreach ($subject_committees as $key => $subject_committee)
                        <h4 class="mt-4">{{ $subject_committee->name }}</h4>
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th style="width:5%">#</th>
                                    <th style="width:40%">Program Name</th>
                                    <th>Total Applicant</th>
                                    <th>Total Admit Card</th>
                                    <th>Pass</th>
                                    <th>Fail</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($subject_committee->programs as $program)
                                <tr>
                                    <td>{{ $sn }}</td>
                                    <td>{{ $program->name }}</td>
                                    <td>{{ $program->exam_apply_count }}</td>
                                    <td>{{ $program->admit_card_count }}</td>
                                    <td>{{ $program->pass_applicant_count }}</td>
                                    <td>@if ($exam->result_published){{ $program->fail_applicant_count }}@else 0 @endif</td>
                                </tr>
                                @php 
                                    $sn++;
                                @endphp
                                @endforeach
                            </tbody>
                        </table>
                        @endforeach
                    </div>
                </div> 
            </div>
            
        </div>
    </div>
@endsection

@section('footer-scripts')
@include('admin.exam.js.student_detail')
@endsection
