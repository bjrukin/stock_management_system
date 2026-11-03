@if ($professional->qualifications->isNotEmpty())
    <div class="col-md-12 col-sm-12 mt-3">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="nav nav-tabs mb-3" id="nav-tab" role="tablist">
                    <button class="nav-link active fw-bold" id="nav-qualifications-tab" data-bs-toggle="tab"
                        data-bs-target="#nav-qualifications" type="button" role="tab"
                        aria-controls="nav-qualifications" aria-selected="true">Qualification Details</button>
                </div>
                <div class="tab-content" id="nav-tabContent">
                    <div class="tab-pane fade show active" id="nav-qualifications" role="tabpanel"
                        aria-labelledby="nav-qualifications-tab" tabindex="0">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered align-middle table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>S.N.</th>
                                                <th>Level</th>
                                                <th>Board / University / Institution</th>
                                                <th>College Name</th>
                                                <th>Passed Year (AD)</th>
                                                <th>Percentage</th>
                                                <th>GPA</th>
                                                <th>PCB Marks</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($professional->qualifications as $count => $qualification)
                                                <tr>
                                                    <td>{{ $count + 1 }}</td>
                                                    <td>{{ $qualification->level->name }}</td>
                                                    <td>{{ $qualification->board_university ?? '' }}</td>
                                                    <td>{{ $qualification->college_name ?? '' }}</td>
                                                    <td>{{ $qualification->passed_year ?? '' }}</td>
                                                    <td>
                                                        @if(isset($qualification->percentage))
                                                            <span class="{{ ($qualification->percentage < ($qualification->eligibility_mark->min_percentage ?? 0)) ? 'text-danger fw-bold' : 'text-success fw-bold' }}">
                                                                {{ $qualification->percentage }}
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if(isset($qualification->gpa))
                                                            <span class="{{ ($qualification->gpa < ($qualification->eligibility_mark->min_gpa ?? 0)) ? 'text-danger fw-bold' : 'text-success fw-bold' }}">
                                                                {{ $qualification->gpa }}
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if(isset($qualification->physics_marks) || isset($qualification->chemistry_marks) || isset($qualification->biology_marks))
                                                            <span class="{{ (isset($qualification->physics_marks) && $qualification->physics_marks < ($qualification->eligibility_mark->min_physics_marks ?? 0)) ? 'text-danger' : 'text-success fw-bold' }}">
                                                                {{ $qualification->physics_marks ?? null }}
                                                            </span> /
                                                            <span class="{{ (isset($qualification->chemistry_marks) && $qualification->chemistry_marks < ($qualification->eligibility_mark->min_chemistry_marks ?? 0)) ? 'text-danger' : 'text-success fw-bold' }}">
                                                                {{ $qualification->chemistry_marks ?? null }}
                                                            </span> /
                                                            <span class="{{ (isset($qualification->biology_marks) && $qualification->biology_marks < ($qualification->eligibility_mark->min_bio_marks ?? 0)) ? 'text-danger' : 'text-success fw-bold' }}">
                                                                {{ $qualification->biology_marks ?? null }}
                                                            </span>
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
    </div>
@endif