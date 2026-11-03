@extends('layout.professional')
@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="col-lg-12 col-sm-12 mb-3">
                <div class="card">
                    <div class="card-body">
                        <h3>{{ $page_title }}</h3>
                    </div>
                </div>
            </div>
            @if ($result['exams']->count() > 0)
                <div class="col-lg-12 col-sm-12 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="mt-0 header-title">Licenced Exam</h4>
                            <div class="table-responsive">
                                <table class="table mb-0">
                                    <thead>
                                        <tr>
                                            <th width="200">Exam Form Open Date</th>
                                            <th width="200">Exam Form End Date</th>
                                            <th>Exam Name</th>
                                            <th>Description</th>
                                            <th width="100">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($result['exams'] as $exam)
                                            <tr>
                                                <td>{{ $exam->opening_date }}</td>
                                                <td>{{ $exam->closing_date }}</td>
                                                <td>{{ $exam->name }}</td>
                                                <td>{{ $exam->description }}</td>
                                                <td><a href="{{ route('professional.exam.application', ['id' => Crypt::encryptString( $exam->id)]) }}"><span class="btn btn-primary">Apply</span></a></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

@endsection
@push('footer-scripts')

@endpush
