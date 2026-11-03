<div class="row">
    <div class="col-lg-6 mb-2">
        <div class="table-responsive">
        <table class="table table-striped table-bordered">
                <tbody>
                    <tr>
                        <td class="fw-bold" width="50%">Name</td>
                        <td width="50%">{{ $professional->info->first_name }}
                            {{ $professional->info->middle_name }}
                            {{ $professional->info->last_name }}
                        </td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Grand Father's Name</td>
                        <td>{{ $professional->info->grandfather_name }} |
                            {{ $professional->info->grandfather_name_nep }}
                        </td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Father's Name</td>
                        <td>{{ $professional->info->father_name }} |
                            {{ $professional->info->father_name_nep }}
                        </td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Mother's Name</td>
                        <td>{{ $professional->info->mother_name }} |
                            {{ $professional->info->mother_name_nep }}
                        </td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Gender</td>
                        <td>{{ ucwords($professional->info->gender )}} </td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Marital Status</td>
                        <td>{{ ucwords($professional->info->marital_status) }} </td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Ethnicity</td>
                        <td>{{ $professional->info->ethinic }} </td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Date of Birth (B.S.) </td>
                        <td>{{ $professional->info->dob_nep }} </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <tbody>
                    <tr>
                        <td class="fw-bold" width="50%">Citizenship Number</td>
                        <td width="50%">{{ $professional->info->citizenship_number }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Citizenship issue date</td>
                        <td>{{ $professional->info->citizenship_issue_date }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Citizenship Issue District</td>
                        <td>{{ $professional->info->citizenship_issue_district }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Province</td>
                        <td>{{ $professional->info->province->name ?? null }} </td>
                    </tr>
                    <tr>
                        <td class="fw-bold">District</td>
                        <td>{{ $professional->info->district->name ?? null }} </td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Municipality</td>
                        <td>{{ $professional->info->municipality->name ?? null }} </td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Ward No</td>
                        <td>{{ $professional->info->ward_no }} </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>