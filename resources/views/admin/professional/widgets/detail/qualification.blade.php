@if ($professional->qualifications->isNotEmpty())
    <div class="col-md-12 col-sm-12">
        <div class="card">
            <div class="card-body">
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                    <button class="nav-link active" id="nav-qualifications-tab" data-bs-toggle="tab"
                        data-bs-target="#nav-qualifications" type="button" role="tab" aria-controls="nav-qualifications"
                        aria-selected="true">Qualification</button>
                </div>
                <div class="tab-pane fade show active" id="qualifications" role="tabpanel"
                    aria-labelledby="qualifications-tab" tabindex="0">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>S.N.</th>
                                            <th>Name</th>
                                            <th>Board / University / Institution</th>
                                            <th>College Name</th>
                                            <th>Passed Year (B.S. / AD)</th>
                                            <th>Percentage </th>
                                            <th>GPA</th>
                                            <th style="width: 15%;">PCB Marks</th>
                                            @if(auth()->guard('admin')->user()->hasPermission('Qualification', 'edit'))
                                                <th>Action</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $count = 1 @endphp
                                        @foreach ($professional->qualifications as $qualification)
                                            @php
                                                $highlightRow = false;
                                                $eligibility = $qualification->eligibility_mark ?? null;
                                                if ($eligibility) {
                                                    if (
                                                        (isset($qualification->percentage) && $qualification->percentage < $eligibility->min_percentage) ||
                                                        (isset($qualification->gpa) && $qualification->gpa < $eligibility->min_gpa) ||
                                                        (isset($qualification->physics_marks) && $qualification->physics_marks < $eligibility->min_physics_marks) ||
                                                        (isset($qualification->chemistry_marks) && $qualification->chemistry_marks < $eligibility->min_chemistry_marks) ||
                                                        (isset($qualification->bio_marks) && $qualification->bio_marks < $eligibility->min_bio_marks)
                                                    ) {
                                                        $highlightRow = true;
                                                    }
                                                }
                                            @endphp
                                            <tr>
                                                <td>{{ $count++ }}</td>
                                                <td>{{ $qualification->level->name }}</td>
                                                <td>{{ $qualification->board_university }}</td>
                                                <td>{{ $qualification->college_name }}</td>
                                                <td>{{ $qualification->passed_year }}</td>
                                                <td>
                                                    <span
                                                        class="{{ (isset($qualification->percentage) && $qualification->percentage < ($qualification->eligibility_mark->min_percentage ?? 0)) ? 'text-danger fw-bold' : 'text-success  fw-bold' }}">
                                                        {{ $qualification->percentage ?? null }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span
                                                        class="{{ (isset($qualification->gpa) && $qualification->gpa < ($qualification->eligibility_mark->min_gpa ?? 0)) ? 'text-danger fw-bold' : 'text-success fw-bold' }}">
                                                        {{ $qualification->gpa ?? null }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if(isset($qualification->physics_marks) || isset($qualification->chemistry_marks) || isset($qualification->biology_marks))
                                                        <span
                                                            class="{{ (isset($qualification->physics_marks) && $qualification->physics_marks < ($qualification->eligibility_mark->min_physics_marks ?? 0)) ? 'text-danger fw-bold' : 'text-success fw-bold' }}">
                                                            {{ $qualification->physics_marks ?? null }}
                                                        </span> /
                                                        <span
                                                            class="{{ (isset($qualification->chemistry_marks) && $qualification->chemistry_marks < ($qualification->eligibility_mark->min_chemistry_marks ?? 0)) ? 'text-danger fw-bold' : 'text-success fw-bold' }}">
                                                            {{ $qualification->chemistry_marks ?? null }}
                                                        </span> /
                                                        <span
                                                            class="{{ (isset($qualification->biology_marks) && $qualification->biology_marks < ($qualification->eligibility_mark->min_bio_marks ?? 0)) ? 'text-danger fw-bold' : 'text-success fw-bold' }}">
                                                            {{ $qualification->biology_marks ?? null }}
                                                        </span>
                                                    @endif
                                                </td>
                                                @if(auth()->guard('admin')->user()->hasPermission('Qualification', 'edit'))
                                                    <td><a
                                                            href="{{ route('admin.professional.qualification', [strtolower($qualification->level->route_name), $professional->id]) }}"><span
                                                                class="mat_sym-icon material-symbols-outlined"><i
                                                                    class="mdi mdi-pencil-outline"></i></span></a></td>
                                                @endif
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