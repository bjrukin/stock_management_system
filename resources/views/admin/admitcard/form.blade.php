@extends('layout.admin')
@section('content')
<link href="https://nepalidatepicker.sajanmaharjan.com.np/v5/nepali.datepicker/css/nepali.datepicker.v5.0.6.min.css" rel="stylesheet" type="text/css"/>
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 col-xl-12">
                <form enctype="multipart/form-data" method="post" action="{{ $action }}" id="form">
                    @csrf
                    <input type="hidden" class="form-control" name="exam_id" value="{{ $id }}">
                    <div class="card">
                        <div class="card-body">
                            <fieldset class="border rounded-3 p-3">
                                <legend>{{$page_title}}</legend>
                                <div class="row">
                                    <div class="mb-3 col-4">
                                        <label class="form-label">Subject Committee *</label>
                                        <select class="form-select select2" name="subject_committee" id="subject_committee">
                                            <option value="">All Subject Committee</option>
                                            @if ($subject_committees->isNotEmpty())
                                            @foreach ($subject_committees as $subject_committee)
                                            <option value="{{ $subject_committee->id }}">
                                                {{ $subject_committee->name }}</option>
                                            @endforeach
                                            @endif
                                        </select>
                                    </div>
                                    <div class="mb-3 col-4">
                                        <label class="form-label">Level *</label>
                                        <select class="form-select select2" name="level" id="level">
                                            <option value="">All level</option>
                                            @if ($levels->isNotEmpty())
                                            @foreach ($levels as $level)
                                            <option value="{{ $level->id }}">
                                                {{ $level->name }}</option>
                                            @endforeach
                                            @endif
                                        </select>
                                    </div>
                                    <div class="mb-3 col-4">
                                        <label class="form-label">Program Name</label>
                                        <select class="form-select select2" name="program" id="program">
                                            <option value="">All program</option>
                                            @if ($programs->isNotEmpty())
                                            @foreach ($programs as $program)
                                            <option value="{{ $program->id }}"
                                                class="{{ $program->level_id }}">{{ $program->name }}</option>
                                            @endforeach
                                            @endif
                                        </select>
                                    </div>
                                    <div class="mb-3 col-4">
                                        <label class="form-label">Exam Date</label>
                                        <input type="text" class="form-control" name="exam_date" id="exam_date">
                                    </div>
                                    <div class="mb-3 col-4">
                                        <label class="form-label">Exam Time</label>
                                        <input type="text" class="form-control" name="exam_time" id="exam_time">
                                    </div>
                                    <div class="mb-3 col-4">
                                        <label class="form-label">Exam Center</label>
                                        <input type="text" class="form-control" name="exam_center" id="exam_center">
                                    </div>
                                    <div class="mb-3 col-12">
                                        <label class="form-label">Exam Instruction</label>
                                        <textarea class="form-control" name="exam_instruction" id="exam_instruction" rows="5">
                                            <p>(क) अनलाइन फाराम भर्दा राखेको फोटोको सक्कल फोटो टाँसी तोकेको स्थानमा दस्तखत (दुवै
                                                फोटोमा पर्ने गरी समेत) र प्रस्ट रेखा देखिने औँठा छाप लगाई प्रवेश पत्र तयार गर्नु
                                                पर्दछ । फोटो नटासेको औँठा छाप नलगाएको र तोकेको स्थानमा दस्तखत नगरेको प्रवेशपत्र
                                                मान्य हुनेछैन । परीक्षा दिन नपाएका वा परीक्षा छुटेका परीक्षार्थीको परीक्षा शुल्क
                                                फिर्ता हुने छैन । </p>
                                            <p>(ख) प्रवेश पत्र रुजु र सुरक्षा जाँच भएपछि परीक्षार्थीले आफुलाई परीक्षा दिन तोकेको
                                                स्थानमा गई बस्नु पर्नेछ । </p>
                                            <p>(ग) परीक्षा शुरु हुनु भन्दा २ घण्टा पहिले परीक्षार्थीले परीक्षा केन्द्रमा उपस्थित भै
                                                सक्नु पर्ने छ । </p>
                                            <p>( घ ) परीक्षा शुरु भएको १५ मिनेट भन्दा पछि आउने लाई परीक्षा मा सामेल गराइने छैन ।
                                            </p>
                                            <p>(ङ) परीक्षा सुरु भएको एक घण्टा अगाडि परीक्षा हल र १ घण्टा अगावै परीक्षा
                                                केन्द्र परिसर छाड्न पाईने छैन ।</p>
                                            <p>(च) परीक्षार्थीले अनिवार्य रुपमा सक्कल नागरीकता, राष्ट्रिय परिचय पत्र वा सवारी चालक
                                                प्रमाण पत्र परीक्षा केन्द्रमा ल्याउनु पर्नेछ । </p>
                                            <p>(छ) परीक्षा केन्द्रभित्र हुल हुज्जत वा होहल्ला गर्न पाइने छैन । </p>
                                            <p>(ज) परीक्षा सञ्चालन गर्ने जिम्मेवारी लिएका सम्पूर्ण व्यक्तिहरूलाई धाक धम्की वा कुटपिट
                                                वा अभद्र व्यवहार गर्न पाइने छैन । </p>
                                            <p>(झ) परीक्षा केन्द्रमा हातहतियार वा विस्फोटक पदार्थ ल्याउन पाइने छैन । </p>
                                            <p>(ञ) परीक्षा कोठामा किताब, कपी, नोटस, चिट तथा मोवाईल जस्ता विद्युतिय उपकरणहरु,
                                                ब्लुटुथ, कालो चस्मा, क्यालकुलेटर, पेन ड्राइभ ल्याउन पाइने छैन । यदि ल्याएको खण्डमा
                                                हराएमा
                                                वा टुटफुट भएमा परिषद् जिम्मेवारी हुने छैन् । </p>
                                            <p>(ट) परीक्षामा यताउति हेर्न, ईसारा गर्न, सार्न, नक्कल गर्न र अन्य परीक्षार्थीसँग
                                                सरसल्लाह समेतका अनुचित काम गर्न पाइने छैन । </p>
                                            <p>(ठ) परीक्षा केन्द्रको सम्पत्तिलाई हानी नोक्सानी पुर्याउन पाइने छैन । </p>
                                            <p>(ड) माथि उल्लेखित निर्देशन विपरित कार्यगर्ने परीक्षार्थीलाई तोकिए बमोजिमको कानुनी
                                                कारबाहि गरिनेछ । </p>
                                            <p style="font-weight : bold !important;">(ढ) परीक्षा को समय १ घण्टा ३० मिनेट, पुर्णाङ्क
                                                १०० र उत्तिर्णाङ्क ५० हुनेछ । </p>
                                        </textarea>
                                    </div>
                                </div>

                            </fieldset>
                            <div class="mt-3"> <button type="button" class="main_btn btn-loading mb-3"
                                    onclick="saveData(this.form)">Submit</button></div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@push('footer-scripts')
@include('admin._partial.imagejs')
@include('admin.admitcard.js.form')
@endpush