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
            <div class="col-lg-12 col-sm-12 mb-3">
                <div class="card">
                    <div class="card-body">
                        <h4 class="mt-0 header-title">TSLC Licenced Voucher</h4>
                        <hr />
                        <div class="table-responsive">
                            <table class="table mb-0">
                                <thead>
                                    <tr>
                                        <th>Exam Name</th>
                                        <th>Description</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>TSLC</td>
                                        <td>यो TSLC तहको नाम दर्ताको लागि मात्र हो | यदि यहाँबाट नाम दर्ता प्रमाणपत्र
                                            परीक्षाको लागि आबेदन दिएर परीक्षाको लागी अयोग्य भएमा परिषद् जवाफ देहि हुने छैन
                                        </td>
                                        <td><a href="{{ route('professional.exam.application') }}"><span
                                                    class="btn btn-primary">Apply</span></a></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('footer-scripts')
@endpush
