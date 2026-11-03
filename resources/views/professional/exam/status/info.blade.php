@if($professional->info)
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-white p-0 border-bottom-0">
                    <ul class="nav nav-tabs card-header-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#personal_info" role="tab">Personal
                                Information</a>
                        </li>
                    </ul>
                </div>
                <div class="tab-content">
                    <div class="tab-pane active" id="personal_info" role="tabpanel">
                        <div class="row">
                            <div class="col-md-2 text-center">
                                @if(isset($professional->info->full_profile_picture))
                                    <a href="{{ $professional->info->full_profile_picture }}" data-fancybox="image">
                                        <img src="{{ $professional->info->full_profile_picture }}" class="img-thumbnail"
                                            style="width: 200px; height: 200px; object-fit: cover;" alt="Profile Picture">
                                    </a>
                                @else
                                    <img src="{{ asset('assets/images/user-dummy-img.jpg') }}" class="img-thumbnail"
                                        style="width: 200px; height: 200px; object-fit: cover;" alt="Profile Picture">
                                @endif
                            </div>

                            <div class="col-md-10">
                                <div class="row g-0">
                                    <div class="col-md-6 border-end">
                                        <table class="table table-striped table-borderless mb-0">
                                            <tbody>
                                                <tr>
                                                    <th class="ps-3" style="width: 40%;">Name</th>
                                                    <td>{{ $professional->info->first_name ?? '' }}
                                                        {{ $professional->info->middle_name ?? '' }}
                                                        {{ $professional->info->last_name ?? '' }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th class="ps-3">Grand Father's Name</th>
                                                    <td>{{ $professional->info->grandfather_name ?? '' }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th class="ps-3">Father's Name</th>
                                                    <td>{{ $professional->info->father_name ?? '' }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th class="ps-3">Mother's Name</th>
                                                    <td>{{ $professional->info->mother_name ?? '' }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th class="ps-3">Gender</th>
                                                    <td>{{ $professional->info->gender ?? '' }}</td>
                                                </tr>
                                                <tr>
                                                    <th class="ps-3">Marital Status</th>
                                                    <td>{{ $professional->info->marital_status ?? '' }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th class="ps-3">Ethnicity</th>
                                                    <td>{{ $professional->info->ethinic ?? '' }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th class="ps-3">Date of Birth (B.S.)</th>
                                                    <td>{{ $professional->info->dob_nep ?? '' }}
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="col-md-6">
                                        <table class="table table-striped table-borderless mb-0">
                                            <tbody>
                                                <tr>
                                                    <th class="ps-3" style="width: 40%;">Citizenship
                                                        Number</th>
                                                    <td>{{ $professional->info->citizenship_number ?? '' }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th class="ps-3">Citizenship issue date</th>
                                                    <td>{{ $professional->info->citizenship_issue_date ?? '' }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th class="ps-3">Citizenship Issue District</th>
                                                    <td>{{ $professional->info->citizenship_issue_district ?? '' }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th class="ps-3">Province</th>
                                                    <td>{{ $professional->info->province->name ?? '' }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th class="ps-3">District</th>
                                                    <td>{{ $professional->info->district->name ?? '' }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th class="ps-3">Municipality</th>
                                                    <td>{{ $professional->info->municipality->name ?? '' }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th class="ps-3">Ward No</th>
                                                    <td>{{ $professional->info->ward_no ?? '' }}
                                                    </td>
                                                </tr>
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
    </div>
@endif