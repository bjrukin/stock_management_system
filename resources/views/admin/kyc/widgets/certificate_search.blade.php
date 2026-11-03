<form method="get" action="" class="">
    <div class="row">
        <div class="col-md-3 col-sm-6 col-xs-12">
            <label class="form-label">Level</label>
            <select class="form-select select2" name="level_id" id="level_id">
                <option value="0">All Levels</option>
                @if($levels->count() > 0)
                @foreach($levels as $level)
                <option value="{{ $level->id }}" @if($level->id == $level_id) selected
                    @endif>{{ $level->short_name_english }}
                </option>
                @endforeach
                @endif
            </select>
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">
            <label class="form-label">Program</label>
            <select class="form-select select2" name="program_id" id="program_id">
                <option value="0" class="0">All Programs</option>
                @if($programs->count() > 0)
                @foreach($programs as $program)
                <option value="{{ $program->id }}" class="{{ $program->level_id }} 0" @if($program->id == $program_id)
                    selected
                    @endif>{{ $program->name }}
                </option>
                @endforeach
                @endif
            </select>
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">
            <label class="form-label">Decision Date</label>
            <select class="form-select select2" name="decision_date">
                <option value="0">Decision Date</option>
                @if($decision_dates->count() > 0)
                @foreach($decision_dates as $date)
                <option value="{{ $date->decision_date }}" @if($date->decision_date == $decision_date) selected
                    @endif>{{ $date->decision_date }}
                </option>
                @endforeach
                @endif
            </select>
        </div>
        <div class="col-md-2 col-sm-6 col-xs-12">
            <label class="form-label">Certificates</label>
            <select class="form-select select2" name="is_printed">
                <option value="">All Certificates</option>
                <option value="Printed" @if($is_printed=='Printed' ) selected @endif>Printed</option>
                <option value="Not Printed" @if($is_printed=='Not Printed' ) selected @endif>Not Printed</option>
            </select>
        </div>
        <div class="col-md-1 col-sm-6 col-xs-12">
            <label class="form-label">&nbsp;</label><br>
            <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i>
            </button>

        </div>
    </div>
</form>