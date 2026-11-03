<div class="row mb-3">
    <div class="col-6">
        <strong>बैंक विवरण</strong><br>
        <span>(क) राष्ट्रिय बाणिज्य बैंक ११५०१००००२१३३००१ </span><br>
        <span>(ख) नेपाल एस वि आई बैंक २०४३५२४०१००००८</span><br>
        <span>(ग) हिमालयन बैंक ००२००५७४६६००१६</span><br>
    </div>
    <div class="col-6">
        <strong>दस्तुर</strong><br>
        @if($exam_id)
        <span>Exam Fee रु. {{ $exam->exam_fee }}</span><br>
        <span>Late Exam Fee रु. {{ $exam->late_fee }} (Start from {{ $exam->late_fee_start_date }})</span><br>
        @else
        <span>(क) विशिष्ठ तहको लागि रु. 3000।</span><br>
        <span>(ख) प्रथम तहको लागि रु. 3000।</span><br>
        <span>(ग) द्वितीय तहको लागि रु. 3000।</span><br>
        @endif
    </div>
</div>