@php use Illuminate\Support\Carbon;
@endphp
@if(count($professional->applicants) > 0)
<div class="col-12">
    <div class="card">
        <div class="card-body">
            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                <button class="nav-link active" id="nav-qualifications-tab" data-bs-toggle="tab"
                    data-bs-target="#nav-qualifications" type="button" role="tab" aria-controls="nav-qualifications"
                    aria-selected="true">Licence Exam Applied</button>
            </div>
            <div class="tab-pane fade show active" id="qualifications" role="tabpanel"
                aria-labelledby="qualifications-tab" tabindex="0">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="table-responsive">
                            <table class="table table-borderless mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <td>S.N.</td>
                                        <td>Exam Name</td>
                                        <td>Payment By</td>
                                        <td>Applied Date (B.S./AD)</td>
                                        <td>Last Updated (B.S./AD)</td>
                                        <td>Program Name</td>
                                        <td>Level</td>
                                        <td>File Status</td>
                                        <td>Status Progress</td>
                                        <td>Symbol Number</td>
                                        <td width="120">Exam Status</td>
                                        <td width="80">Action</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $count = 1 @endphp
                                    @foreach($professional->applicants as $applicant)
                                    @php
                                    if ($applicant->is_passed == 0 && $applicant->is_admit_card_generate == 1)
                                    $status = 'Failed';
                                    elseif ($applicant->is_passed == 0 && $applicant->attempt >= 2)
                                    $status = 'Re Exam';
                                    elseif ($applicant->status == 'progress' && $applicant->state == 'exam_committee')
                                    $status = 'Re Exam';
                                    elseif ($applicant->rejected == 1)
                                    $status = 'Rejeted';
                                    elseif ($applicant->state != 'operator')
                                    $status = 'Accepted';
                                    else
                                    $status = 'New Applied';
                                    @endphp
                                    <tr>
                                        <td style="background-color: {{ $colors[$applicant->level_id] ?? '#ffffff' }}">{{ $count++}}</td>
                                        <td style="background-color: {{ $colors[$applicant->level_id] ?? '#ffffff' }}">{{ $applicant->exam->name ?? null }}</td>
                                        @if($applicant->full_voucher_image)
                                        <td style="background-color: {{ $colors[$applicant->level_id] ?? '#ffffff' }}"><a href="{{ $applicant->full_voucher_image  }}" data-fancybox="image"><img
                                                    style="height:170px" src="{{ $applicant->full_voucher_image  }}"
                                                    height="250"></a></td>
                                        @else
                                        <td style="background-color: {{ $colors[$applicant->level_id] ?? '#ffffff' }}">{{ $applicant->payment->total_amount ?? null }}</td>
                                        @endif
                                        <td style="background-color: {{ $colors[$applicant->level_id] ?? '#ffffff' }}">{{ \Carbon\Carbon::parse($applicant->created_at)->format('Y-m-d') }}</td>
                                        <td style="background-color: {{ $colors[$applicant->level_id] ?? '#ffffff' }}">{{ \Carbon\Carbon::parse($applicant->updated_at)->format('Y-m-d') }}</td>
                                        <td style="background-color: {{ $colors[$applicant->level_id] ?? '#ffffff' }}">{{ $applicant->program?->name }}</td>
                                        <td style="background-color: {{ $colors[$applicant->level_id] ?? '#ffffff' }}">
                                            {{ $applicant->level?->name }}</td>
                                        <td style="background-color: {{ $colors[$applicant->level_id] ?? '#ffffff' }}">{{ ucwords(strtolower(str_replace('_', ' ', $applicant->state))) }}</td>
                                        <td style="background-color: {{ $colors[$applicant->level_id] ?? '#ffffff' }}">{{ $applicant->status }}</td>
                                        <td style="background-color: {{ $colors[$applicant->level_id] ?? '#ffffff' }}">{{ $applicant->admit_card->symbol_number ?? null }}</td>
                                        <td style="background-color: {{ $colors[$applicant->level_id] ?? '#ffffff' }}">{{ $status }}</td>
                                        <td style="background-color: {{ $colors[$applicant->level_id] ?? '#ffffff' }}">@if(auth()->guard('admin')->user()->hasPermission('Applicant', 'edit'))
                                            <a href="{{ route('admin.applicant.addEdit', ['id' => $applicant->id]) }}"><span
                                                    class="mat_sym-icon material-symbols-outlined"><i
                                                        class="mdi mdi-pencil-outline"></i></span> </a>
                                            @endif

                                            @if(auth()->guard('admin')->user()->hasPermission('Applicant', 'delete'))
                                            <a href="javascript::void(0)"
                                                onclick="deleteApplicant('{{ $applicant->id }}')"><span
                                                    class="mat_sym-icon material-symbols-outlined"> <i
                                                        class="mdi mdi-delete-forever-outline"></i> </span> </a>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif