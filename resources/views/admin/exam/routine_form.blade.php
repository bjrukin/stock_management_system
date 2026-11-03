@extends('layout.admin')
@section('content')
<link href="https://nepalidatepicker.sajanmaharjan.com.np/v5/nepali.datepicker/css/nepali.datepicker.v5.0.6.min.css" rel="stylesheet" type="text/css"/>
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 col-xl-6">
                <form enctype="multipart/form-data" method="get" action="{{ $action }}" id="form">
                    @csrf
                    <input type="hidden" class="form-control" name="id" value="{{ $exam->id }}">
                    <div class="card">
                        <div class="card-body">
                            <fieldset class="border rounded-3 p-3">
                                <legend>{{$page_title}}</legend>
                                <div class="row">
                                    <div class="mb-3">
                                        <label class="form-label">Number of Students Per Shift</label>
                                        <input type="text" class="form-control" name="student_number" id="student_number">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Number of Shift</label>
                                        <input type="text" class="form-control" name="shift_number" id="shift_number">
                                    </div>
                                    
                                </div>

                            </fieldset>
                            <div class="mt-3"> <button class="main_btn btn-loading mb-3"
                                    onclick="saveData(this.form)">Submit</button></div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-xl-12">
                <div class="card" id="routine">
                    @if($row)
                    <div class="card-body">
                        <h4 class="header-title mt-0 mb-3">Exam Routine</h4>
                        @foreach($row as $day => $shifts)
                        <div class="mb-3">
                            <h5>Day {{ $day }}</h5>
                            @foreach($shifts as $shift => $shift_programs)
                            <div class="mb-2">
                                <h6>Shift {{ $shift }}</h6>
                                <table class="table table-bordered table-striped table-hover">
                                    @foreach($shift_programs as $program)
                                    {{-- {{ dd($program) }} --}}
                                    <tr>
                                        <td style="width:50%">
                                            <select class="select2" id="program">
                                                @foreach($programs as $program_option)
                                                <option @if($program_option['program_id'] == $program['program_id']) selected @endif>{{ $program_option->name }}
                                                @endforeach
                                            </select>
                                        </td>
                                        <td style="width:20%">{{ $program['starting_symbol_number'] }}</td>
                                        <td style="width:20%">{{ $program['ending_symbol_number'] }}</td>
                                        <td style="width:10%">{{ $program['count'] }}</td>
                                    </tr>
                                    @endforeach
                                </table>
                            </div>
                            @endforeach
                        </div>
                        @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('footer-scripts')
@include('admin._partial.imagejs')
@include('admin.exam.js.routine_form')
@endpush