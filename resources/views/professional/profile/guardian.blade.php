@extends('layout.professional')
@section('content')
    <div class="content">
        <div class="container-fluid">
            @include('professional.profile.widgets.breadcrumb')
            @include('professional.profile.widgets.note')
            <div class="form-section">
                <form id="form" method="post" action="{{ route('professional.profile.save.guardian') }}">
                    @csrf
                    <div class="card">
                        <div class="card-body">
                            <fieldset class="border rounded-3 p-3">
                                <legend>{{$page_title}}</legend>
                                <div class="row">
                                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-3">
                                        <label class="form-label">Father Name *</label>
                                        <input class="form-control" name="father_name"
                                            value="{{ $professional->father_name ?? old('father_name') }}" type="text">
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-3">
                                        <label class="form-label">बुबाको नाम</label>
                                        <input class="form-control" name="father_name_nep"
                                            value="{{ $professional->father_name_nep ?? old('father_name_nep') }}"
                                            type="text">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-3">
                                        <label class="form-label">Mother Name *</label>
                                        <input class="form-control" name="mother_name"
                                            value="{{ $professional->mother_name ?? old('mother_name') }}" type="text">
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-3">
                                        <label class="form-label">आमाको नाम</label>
                                        <input class="form-control" name="mother_name_nep"
                                            value="{{ $professional->mother_name_nep ?? old('mother_name_nep') }}"
                                            type="text">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-3">
                                        <label class="form-label">Grandfather Name *</label>
                                        <input class="form-control" name="grandfather_name"
                                            value="{{ $professional->grandfather_name ?? old('grandfather_name') }}"
                                            type="text">
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-12  mb-3">
                                        <label class="form-label">हजुरबुबाको नाम </label>
                                        <input class="form-control" name="grandfather_name_nep"
                                            value="{{ $professional->grandfather_name_nep ?? old('grandfather_name_nep') }}"
                                            type="text">
                                    </div>
                                </div>
                            </fieldset>


                            <div class="mt-3">
                                <a href="{{ route('professional.profile.personal')}}" class="btn btn-primary w-25"> Back</a>
                                <button type="button" class="main_btn btn-loading mb-3"
                                    onclick="saveData(this.form)">Next</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>




        </div>
@endsection
    @push('footer-scripts')
        @include('professional.profile.js.guardian')
    @endpush