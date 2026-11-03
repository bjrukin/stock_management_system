<form method="get" action="" class="">
    <div class="row">
        <div class="col-md-4 col-sm-6 col-xs-12">
            <label class="form-label">Exam</label>
            <select class="form-select select2" name="exam_id">
                <option value="0" @if($exam_id==0) selected @endif>All </option>
                @if($exams->count() > 0)
                @foreach($exams as $exam)
                <option value="{{ $exam->id }}" @if($exam->id == $exam_id) selected
                    @endif>{{ $exam->name }}
                </option>
                @endforeach
                @endif
                <option value="tslc" @if($exam_id=="tslc" ) selected @endif>TSLC</option>
            </select>
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">
            <label class="form-label">Level</label>
            <select class="form-select select2" name="level_id">
                <option value="0" @if($level_id==0) selected @endif>All Levels</option>
                @if($levels->count() > 0)
                @foreach($levels as $level)
                <option value="{{ $level->id }}" @if($level->id == $level_id) selected
                    @endif>{{ $level->short_name_english }}
                </option>
                @endforeach
                @endif
            </select>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
            <label class="form-label">Program</label>
            <select class="form-select select2" name="program_id">
                <option value="" @if($program_id=="" ) selected @endif>All Programs</option>
                @if($programs->count() > 0)
                @foreach($programs as $program)
                <option value="{{ $program->id }}" @if($program->id == $program_id) selected
                    @endif>{{ $program->name }}
                </option>
                @endforeach
                @endif
            </select>
        </div>
        <div class="col-md-1 col-sm-6 col-xs-12">
            <label class="form-label">&nbsp;</label><br>
            <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i>
            </button>

        </div>
    </div>
</form>