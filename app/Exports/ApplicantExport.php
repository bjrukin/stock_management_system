<?php

namespace App\Exports;

use App\Models\Applicant;
use App\Models\Exam;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ApplicantExport implements FromQuery, ShouldAutoSize, WithMapping, WithHeadings, WithTitle, WithEvents {

    protected $request;
    protected $title;
    protected $index = 0;
    public function __construct($request) {
        $this->request = $request;
        $exam = Exam::where('id', $this->request['exam_id'])->first();
        $this->title = $exam->name ?? 'TSLC' . ' -  Applicant List';
    }

    /**
     * @return \Illuminate\Support\Collection
     */

    public function query() {
        $exam_id = $this->request['exam_id'];
        $level_id = $this->request['level_id'];
        $program_id = $this->request['program_id'];
        $status = $this->request['status'];
        $state = $this->request['state'];
        $data = Applicant::select('*')->with(['professional', 'professional.qualification', 'professional_info']);
        if ($exam_id == 'tslc') {
            $data->whereNull('exam_id');
        } else {
            $data->where('exam_id', $exam_id);
        }
        if ($level_id) {
            $data->where('applicants.level_id', $level_id);
        }
        if ($state) {
            $data->where('state', $state);
        }
        if ($status) {
            $data->where('status', $status);
        }
        return $data;
    }

    public function map($data): array {
        return [
            ++$this->index,
            $data->professional_info->full_name,
            $data->professional_info->citizenship_number,
            $data->professional_info->dob_nep,
            $data->professional->phone,
            $data->professional->email,
            Carbon::parse($data->created_at)->format('Y-m-d'),
            $data->level->short_name_english,
            $data->program->name,
            $data->professional->qualification->college_name ?? null,
            $data->status
        ];
    }

    public function title(): string {
        return $this->title;
    }

    public function headings(): array {
        $header1 = [$this->title];
        $header2 = ['S.N', 'Name', 'Citizenship', 'Date of Birth', 'Phone', 'Email', 'Applied Date', 'Level', 'Program', 'College', 'Status'];
        return [$header1, $header2];
    }

    public function registerEvents(): array {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->mergeCells('A1:L1');
                $event->sheet->getStyle('A1')->applyFromArray([
                    'font'      => [
                        'size' => '14px',
                        'bold' => true,
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                    ],
                ]);

                $event->sheet->getStyle('A2:L1')->applyFromArray([
                    'font' => [
                        'size' => '12px',
                        'bold' => true,
                    ],
                ]);
            },
        ];
    }
}
