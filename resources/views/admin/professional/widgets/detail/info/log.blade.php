<div class="row">
    <div class="col-lg-12">
        <div class="table-responsive">
            <table class="table table-striped table-borderless mb-0">
                <thead class="table-dark">
                    <tr>
                        <th class="text-bold" style="width:15%">Date</th>
                        <th class="text-bold" style="width:15%">State</th>
                        <th class="text-bold" style="width:10%">Status</th>
                        <th style="width:60%">Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach (['operator', 'officer', 'registrar', 'subject_committee', 'exam_committee'] as $state)
                    @php
                    $logs = $applicant_logs->where('state', $state);
                    $lastLog = $logs->sortByDesc('id')->first();
                    @endphp
                    @if ($logs->isNotEmpty())
                    @foreach ($logs as $log)
                    <tr>
                        <td>{{ $log->created_at }}</td>
                        <td class="text-bold">{{ ucfirst(str_replace('_', ' ', $state)) }}</td>
                        <td>{{ $log->status }}</td>
                        <td>{{ $log->remarks }}</td>
                    </tr>
                    @endforeach
                    @else
                    <tr>
                        <td class="text-bold">{{ ucfirst(str_replace('_', ' ', $state)) }}</td>
                        <td>{{ ($lastest_applicant && $lastest_applicant->state ==  $state) ? 'Progress' : '-' }} </td>
                        <td></td>
                        <td></td>
                    </tr>
                    @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>