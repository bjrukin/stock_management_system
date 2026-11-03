<?php
namespace App\Services\Admin;

use App\Exports\ApplicantExport;
use App\Jobs\Admin\ApplicantStatusJob;
use App\Models\Applicant;
use App\Models\ApplicantLog;
use App\Models\Exam;
use App\Models\VerificationStep;
use App\Traits\StoreImageTrait;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class ApplicantService
{
    use StoreImageTrait;

    public function list($exam_id, $level_id, $status, $program_id, $state)
    {
        try {
            $data = Applicant::with(['professional:id,name,email', 'professional_info:professional_id,dob_nep,citizenship_number', 'exam:id,name', 'level:id,name'])->select('*'); //->where('payment_success', 1);
            if ($exam_id) {
                if ($exam_id == 'tslc') {
                    $data->whereNull('exam_id');
                } else {
                    $data->where('exam_id', $exam_id);
                }
            }
            if ($level_id) {
                $data->where('level_id', $level_id);
            }
            if ($program_id) {
                $data->where('program_id', $program_id);
            }
            if ($status) {
                $data->where('status', $status);
            }
            if ($state) {
                $data->where('state', $state);
            }
            return DataTables::of($data)
                ->filterColumn('professional_name', function ($query, $keyword) {
                    $query->whereHas('professional', function ($qry) use ($keyword) {
                        $qry->whereAny(['name', 'email'], 'LIKE', '%' . $keyword . '%');
                    });
                })
                ->filterColumn('exam_name', function ($query, $keyword) {
                    $query->whereHas('exam', function ($qry) use ($keyword) {
                        $qry->where('name', 'LIKE', '%' . $keyword . '%');
                    });
                })
                ->filterColumn('level_name', function ($query, $keyword) {
                    $query->whereHas('level', function ($qry) use ($keyword) {
                        $qry->where('name', 'LIKE', '%' . $keyword . '%');
                    });
                })
                ->orderColumn('professional_name', function ($query, $order) {
                    $query->orderBy(
                        Applicant::select('name')->from('professionals')->whereColumn('professionals.id', 'applicants.professional_id'),
                        $order
                    );
                })
                ->orderColumn('applied_date', function ($query, $order) {
                    $query->orderBy('created_at', $order);
                })
                ->addIndexColumn()
                ->addColumn('professional_name', function ($row) {
                    return ucwords(strtolower($row->professional->name)) ?? null;
                })
                ->addColumn('citizenship_number', function ($row) {
                    return $row->professional_info->citizenship_number ?? null;
                })
                ->addColumn('dob_nep', function ($row) {
                    return $row->professional_info->dob_nep ?? null;
                })
                ->addColumn('email', function ($row) {
                    return $row->professional->email ?? null;
                })
                ->addColumn('applied_date', function ($row) {
                    return Carbon::parse($row->created_at)->format('Y-m-d');
                })
                ->addColumn('exam_name', function ($row) {
                    return $row->exam->name ?? null;
                })
                ->addColumn('level_name', function ($row) {
                    return $row->level->name ?? null;
                })
                ->addColumn('status', function ($row) {
                    $colors = [
                        'accepted' => '#008000',
                        'rejected' => '#ff0000',
                        'progress' => '#00a1ff',
                        're-exam' => '#ff7600',
                        're-check' => '#ff7600',
                        'onhold' => '#808080',
                        'pending' => '#808080',
                    ];
                    $color = $colors[$row->status] ?? '#808080';
                    return '<span style="
                        background-color:' . $color . ';
                        color:#fff;
                        padding:3px;
                        border-radius:4px;
                    ">' . ucwords($row->status) . '</span>';
                })
                ->addColumn('state', function ($row) {
                    return ucwords(strtolower(str_replace('_', ' ', $row->state)));
                })
                ->addColumn('action', function ($row) {
                    $btn = '';
                    $editUrl = route('admin.professional.detail', ['id' => $row->professional_id]);
                    $btn .= '<a href="' . $editUrl . '"><span
                    class="mat_sym-icon material-symbols-outlined"><i class="mdi mdi-eye-outline"></i></span></a>';
                    if (auth()->guard('admin')->user()->hasPermission('Applicant', 'delete')) {
                        $btn .= ' <a href="javascript:void(0)" onclick="deleteData(' . $row->id . ')"><span class="mat_sym-icon material-symbols-outlined"><i class="mdi mdi-delete-forever-outline"></i> </span>
                       </a>';
                    }
                    if ($row->admit_card) {
                        $url = route('admin.applicant.admitcard', [$row->id]);
                        $btn .= ' <a href="' . $url . '"><span class="mat_sym-icon material-symbols-outlined"><i class="mdi mdi-card-account-details-outline"></i> </span></a>';
                    }
                    return $btn;
                })
                ->orderColumn('DT_RowIndex', 'applicants.id $1')
                ->rawColumns(['status', 'action'])
                ->make(true);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function new($exam_id, $level_id, $status, $program_id, $state)
    {
        try {
            $data = Applicant::with(['professional:id,name,email', 'professional_info:professional_id,dob_nep,citizenship_number', 'exam:id,name', 'level:id,name'])->select('*')->where('payment_success', 1);
            if ($exam_id) {
                if ($exam_id == 'tslc') {
                    $data->whereNull('exam_id');
                } else {
                    $data->where('exam_id', $exam_id);
                }
            }
            if ($level_id) {
                $data->where('level_id', $level_id);
            }
            if ($program_id) {
                $data->where('program_id', $program_id);
            }
            if ($status) {
                $data->where('status', $status);
            }
            if ($state) {
                $data->where('state', $state);
            }
            return DataTables::of($data)
                ->filterColumn('professional_name', function ($query, $keyword) {
                    $query->whereHas('professional', function ($qry) use ($keyword) {
                        $qry->whereAny(['name', 'email'], 'LIKE', '%' . $keyword . '%');
                    });
                })
                ->filterColumn('exam_name', function ($query, $keyword) {
                    $query->whereHas('exam', function ($qry) use ($keyword) {
                        $qry->where('name', 'LIKE', '%' . $keyword . '%');
                    });
                })
                ->filterColumn('level_name', function ($query, $keyword) {
                    $query->whereHas('level', function ($qry) use ($keyword) {
                        $qry->where('name', 'LIKE', '%' . $keyword . '%');
                    });
                })
                ->orderColumn('professional_name', function ($query, $order) {
                    $query->orderBy(
                        Applicant::select('name')->from('professionals')->whereColumn('professionals.id', 'applicants.professional_id'),
                        $order
                    );
                })
                ->orderColumn('applied_date', function ($query, $order) {
                    $query->orderBy('created_at', $order);
                })
                ->addIndexColumn()
                ->addColumn('professional_name', function ($row) {
                    return ucwords(strtolower($row->professional->name)) ?? null;
                })
                ->addColumn('citizenship_number', function ($row) {
                    return $row->professional_info->citizenship_number ?? null;
                })
                ->addColumn('dob_nep', function ($row) {
                    return $row->professional_info->dob_nep ?? null;
                })
                ->addColumn('email', function ($row) {
                    return $row->professional->email ?? null;
                })
                ->addColumn('applied_date', function ($row) {
                    return Carbon::parse($row->created_at)->format('Y-m-d');
                })
                ->addColumn('exam_name', function ($row) {
                    return $row->exam->name ?? null;
                })
                ->addColumn('level_name', function ($row) {
                    return $row->level->name ?? null;
                })
                ->addColumn('status', function ($row) {
                    $colors = [
                        'accepted' => '#008000',
                        'rejected' => '#ff0000',
                        'progress' => '#00a1ff',
                        're-exam' => '#ff7600',
                        're-check' => '#ff7600',
                        'onhold' => '#808080',
                        'pending' => '#808080',
                    ];
                    $color = $colors[$row->status] ?? '#808080';
                    return '<span style="
                        background-color:' . $color . ';
                        color:#fff;
                        padding:3px;
                        border-radius:4px;
                    ">' . ucwords($row->status) . '</span>';
                })
                ->addColumn('state', function ($row) {
                    return ucwords(strtolower(str_replace('_', ' ', $row->state)));
                })
                ->addColumn('action', function ($row) {
                    $btn = '';
                    $editUrl = route('admin.professional.detail', ['id' => $row->professional_id]);
                    $btn .= '<a href="' . $editUrl . '"><span
                    class="mat_sym-icon material-symbols-outlined"><i class="mdi mdi-eye-outline"></i></span></a>';
                    if (auth()->guard('admin')->user()->hasPermission('Applicant', 'delete')) {
                        $btn .= ' <a href="javascript:void(0)" onclick="deleteData(' . $row->id . ')"><span class="mat_sym-icon material-symbols-outlined"><i class="mdi mdi-delete-forever-outline"></i> </span>

                       </a>';
                    }
                    return $btn;
                })
                ->orderColumn('DT_RowIndex', 'applicants.id $1')
                ->rawColumns(['status', 'action'])
                ->make(true);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function pending($exam_id, $level_id, $program_id, $state)
    {
        try {
            $data = Applicant::with(['professional:id,name,email', 'professional_info:professional_id,dob_nep,citizenship_number', 'exam:id,name', 'level:id,name'])->select('*')->where('payment_success', 1)->whereIn('status', ['pending', 'onhold']);
            if ($exam_id) {
                if ($exam_id == 'tslc') {
                    $data->whereNull('exam_id');
                } else {
                    $data->where('exam_id', $exam_id);
                }
            }
            if ($level_id) {
                $data->where('level_id', $level_id);
            }
            if ($program_id) {
                $data->where('program_id', $program_id);
            }
            if ($state) {
                $data->where('state', $state);
            }
            return DataTables::of($data)
                ->filterColumn('professional_name', function ($query, $keyword) {
                    $query->whereHas('professional', function ($qry) use ($keyword) {
                        $qry->whereAny(['name', 'email'], 'LIKE', '%' . $keyword . '%');
                    });
                })
                ->filterColumn('exam_name', function ($query, $keyword) {
                    $query->whereHas('exam', function ($qry) use ($keyword) {
                        $qry->where('name', 'LIKE', '%' . $keyword . '%');
                    });
                })
                ->filterColumn('level_name', function ($query, $keyword) {
                    $query->whereHas('level', function ($qry) use ($keyword) {
                        $qry->where('name', 'LIKE', '%' . $keyword . '%');
                    });
                })
                ->orderColumn('professional_name', function ($query, $order) {
                    $query->orderBy(
                        Applicant::select('name')->from('professionals')->whereColumn('professionals.id', 'applicants.professional_id'),
                        $order
                    );
                })
                ->orderColumn('applied_date', function ($query, $order) {
                    $query->orderBy('created_at', $order);
                })
                ->addIndexColumn()
                ->addColumn('professional_name', function ($row) {
                    return ucwords(strtolower($row->professional->name)) ?? null;
                })
                ->addColumn('email', function ($row) {
                    return $row->professional->email ?? null;
                })
                ->addColumn('citizenship_number', function ($row) {
                    return $row->professional_info->citizenship_number ?? null;
                })
                ->addColumn('dob_nep', function ($row) {
                    return $row->professional_info->dob_nep ?? null;
                })
                ->addColumn('exam_name', function ($row) {
                    return $row->exam->name ?? null;
                })
                ->addColumn('level_name', function ($row) {
                    return $row->level->name ?? null;
                })
                ->addColumn('applied_date', function ($row) {
                    return Carbon::parse($row->created_at)->format('Y-m-d');
                })
                ->addColumn('status', function ($row) {
                    $colors = [
                        'accepted' => '#008000',
                        'rejected' => '#ff0000',
                        'progress' => '#00a1ff',
                        're-exam' => '#ff7600',
                        're-check' => '#ff7600',
                        'onhold' => '#808080',
                        'pending' => '#808080',
                    ];
                    $color = $colors[$row->status] ?? '#808080';
                    return '<span style="
                        background-color:' . $color . ';
                        color:#fff;
                        padding:3px;
                        border-radius:4px;
                    ">' . ucwords($row->status) . '</span>';
                })
                ->addColumn('state', function ($row) {
                    return ucwords(strtolower(str_replace('_', ' ', $row->state)));
                })
                ->addColumn('action', function ($row) {
                    $btn = '';
                    $editUrl = route('admin.professional.detail', ['id' => $row->professional_id]);
                    $btn .= '<a href="' . $editUrl . '"><span
                    class="mat_sym-icon material-symbols-outlined"><i class="mdi mdi-eye-outline"></i></span></a>';
                    if (auth()->guard('admin')->user()->hasPermission('Applicant', 'delete')) {
                        $btn .= ' <a href="javascript:void(0)" onclick="deleteData(' . $row->id . ')"><span class="mat_sym-icon material-symbols-outlined"><i class="mdi mdi-delete-forever-outline"></i> </span></a>';
                    }
                    return $btn;
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function approved($exam_id, $level_id, $program_id)
    {
        try {
            $admin = auth()->guard('admin')->user();
            $state = strtolower(str_replace(' ', '_', $admin->role->name));
            $status = 'accepted';
            $data = Applicant::with(['professional:id,name,email', 'professional_info:professional_id,dob_nep,citizenship_number', 'exam:id,name', 'level:id,name'])->select('*')->where('payment_success', 1)->whereHas('logs', fn($qry) => $qry->where(['status' => $status, 'state' => $state]));
            if ($admin->role_id == 6) {
                $data->whereHas('logs', fn($qry) => $qry->where('admin_id', auth()->guard('admin')->id()));
            }
            if ($exam_id) {
                if ($exam_id == 'tslc') {
                    $data->whereNull('exam_id');
                } else {
                    $data->where('exam_id', $exam_id);
                }
            }
            if ($level_id) {
                $data->where('level_id', $level_id);
            }
            if ($program_id) {
                $data->where('program_id', $program_id);
            }
            return DataTables::of($data)
                ->filterColumn('professional_name', function ($query, $keyword) {
                    $query->whereHas('professional', function ($qry) use ($keyword) {
                        $qry->whereAny(['name', 'email'], 'LIKE', '%' . $keyword . '%');
                    });
                })
                ->filterColumn('exam_name', function ($query, $keyword) {
                    $query->whereHas('exam', function ($qry) use ($keyword) {
                        $qry->where('name', 'LIKE', '%' . $keyword . '%');
                    });
                })
                ->filterColumn('level_name', function ($query, $keyword) {
                    $query->whereHas('level', function ($qry) use ($keyword) {
                        $qry->where('name', 'LIKE', '%' . $keyword . '%');
                    });
                })
                ->orderColumn('professional_name', function ($query, $order) {
                    $query->orderBy(
                        Applicant::select('name')->from('professionals')->whereColumn('professionals.id', 'applicants.professional_id'),
                        $order
                    );
                })
                ->orderColumn('applied_date', function ($query, $order) {
                    $query->orderBy('created_at', $order);
                })
                ->addIndexColumn()
                ->addColumn('professional_name', function ($row) {
                    return ucwords(strtolower($row->professional->name)) ?? null;
                })
                ->addColumn('citizenship_number', function ($row) {
                    return $row->professional_info->citizenship_number ?? null;
                })
                ->addColumn('dob_nep', function ($row) {
                    return $row->professional_info->dob_nep ?? null;
                })
                ->addColumn('email', function ($row) {
                    return $row->professional->email ?? null;
                })
                ->addColumn('applied_date', function ($row) {
                    return Carbon::parse($row->created_at)->format('Y-m-d');
                })
                ->addColumn('exam_name', function ($row) {
                    return $row->exam->name ?? null;
                })
                ->addColumn('level_name', function ($row) {
                    return $row->level->name ?? null;
                })
                ->addColumn('status', function ($row) {
                    $colors = [
                        'accepted' => '#008000',
                        'rejected' => '#ff0000',
                        'progress' => '#00a1ff',
                        're-exam' => '#ff7600',
                        're-check' => '#ff7600',
                        'onhold' => '#808080',
                        'pending' => '#808080',
                    ];
                    $color = $colors[$row->status] ?? '#808080';
                    return '<span style="
                        background-color:' . $color . ';
                        color:#fff;
                        padding:3px;
                        border-radius:4px;
                    ">' . ucwords($row->status) . '</span>';
                })
                ->addColumn('state', function ($row) {
                    return ucwords(strtolower(str_replace('_', ' ', $row->state)));
                })
                ->addColumn('action', function ($row) use ($admin) {
                    $btn = '';
                    $editUrl = route('admin.professional.detail', ['id' => $row->professional_id]);
                    $btn .= '<a href="' . $editUrl . '"><span
                    class="mat_sym-icon material-symbols-outlined"><i class="mdi mdi-eye-outline"></i></span></a>';
                    if ($admin->hasPermission('Applicant', 'delete')) {
                        $btn .= ' <a href="javascript:void(0)" onclick="deleteData(' . $row->id . ')"><span class="mat_sym-icon material-symbols-outlined"><i class="mdi mdi-delete-forever-outline"></i> </span></a>';
                    }
                    return $btn;
                })
                ->rawColumns(['status', 'action'])
                ->make(true);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function rejected($exam_id, $level_id, $program_id)
    {
        try {
            $admin = auth()->guard('admin')->user();
            $state = strtolower(str_replace(' ', '_', $admin->role->name));
            $status = 'rejected';
            $data = Applicant::with(['professional:id,name,email', 'professional_info:professional_id,dob_nep,citizenship_number', 'exam:id,name', 'level:id,name'])->select('*')->where(['status' => $status, 'state' => $state, 'payment_success' => 1]);
            if ($admin->role_id == 6) {
                $data->whereHas('logs', fn($qry) => $qry->where('admin_id', auth()->guard('admin')->id()));
            }
            if ($exam_id) {
                if ($exam_id == 'tslc') {
                    $data->whereNull('exam_id');
                } else {
                    $data->where('exam_id', $exam_id);
                }
            }
            if ($level_id) {
                $data->where('level_id', $level_id);
            }
            if ($program_id) {
                $data->where('program_id', $program_id);
            }
            return DataTables::of($data)
                ->filterColumn('professional_name', function ($query, $keyword) {
                    $query->whereHas('professional', function ($qry) use ($keyword) {
                        $qry->whereAny(['name', 'email'], 'LIKE', '%' . $keyword . '%');
                    });
                })
                ->filterColumn('exam_name', function ($query, $keyword) {
                    $query->whereHas('exam', function ($qry) use ($keyword) {
                        $qry->where('name', 'LIKE', '%' . $keyword . '%');
                    });
                })
                ->filterColumn('level_name', function ($query, $keyword) {
                    $query->whereHas('level', function ($qry) use ($keyword) {
                        $qry->where('name', 'LIKE', '%' . $keyword . '%');
                    });
                })
                ->orderColumn('professional_name', function ($query, $order) {
                    $query->orderBy(
                        Applicant::select('name')->from('professionals')->whereColumn('professionals.id', 'applicants.professional_id'),
                        $order
                    );
                })
                ->orderColumn('applied_date', function ($query, $order) {
                    $query->orderBy('created_at', $order);
                })
                ->addIndexColumn()
                ->addColumn('professional_name', function ($row) {
                    return ucwords(strtolower($row->professional->name)) ?? null;
                })
                ->addColumn('citizenship_number', function ($row) {
                    return $row->professional_info->citizenship_number ?? null;
                })
                ->addColumn('dob_nep', function ($row) {
                    return $row->professional_info->dob_nep ?? null;
                })
                ->addColumn('email', function ($row) {
                    return $row->professional->email ?? null;
                })
                ->addColumn('applied_date', function ($row) {
                    return Carbon::parse($row->created_at)->format('Y-m-d');
                })
                ->addColumn('exam_name', function ($row) {
                    return $row->exam->name ?? null;
                })
                ->addColumn('level_name', function ($row) {
                    return $row->level->name ?? null;
                })
                ->addColumn('status', function ($row) {
                    $colors = [
                        'accepted' => '#008000',
                        'rejected' => '#ff0000',
                        'progress' => '#00a1ff',
                        're-exam' => '#ff7600',
                        're-check' => '#ff7600',
                        'onhold' => '#808080',
                        'pending' => '#808080',
                    ];
                    $color = $colors[$row->status] ?? '#808080';
                    return '<span style="
                        background-color:' . $color . ';
                        color:#fff;
                        padding:3px;
                        border-radius:4px;
                    ">' . ucwords($row->status) . '</span>';
                })
                ->addColumn('state', function ($row) {
                    return ucwords(strtolower(str_replace('_', ' ', $row->state)));
                })
                ->addColumn('action', function ($row) use ($admin) {
                    $btn = '';
                    $editUrl = route('admin.professional.detail', ['id' => $row->professional_id]);
                    $btn .= '<a href="' . $editUrl . '"><span
                    class="mat_sym-icon material-symbols-outlined"><i class="mdi mdi-eye-outline"></i></span></a>';
                    if ($admin->hasPermission('Applicant', 'delete')) {
                        $btn .= ' <a href="javascript:void(0)" onclick="deleteData(' . $row->id . ')"><span class="mat_sym-icon material-symbols-outlined"><i class="mdi mdi-delete-forever-outline"></i> </span></a>';
                    }
                    return $btn;
                })
                ->rawColumns(['status', 'action'])
                ->make(true);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    function export($request)
    {
        try {
            $exam = Exam::where('id', $request['exam_id'])->first();
            $exam_name = $exam->name ?? 'tslc';
            return Excel::download(new ApplicantExport($request), $exam_name . '_applicant_list.xlsx');
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
    public function store($request)
    {
        try {
            $application = Applicant::where('id', $request['id'])->first();
            if (preg_match('#^data:image.*?base64,#', $request['voucher_image'])) {
                $voucher_image = $this->StoreBase64Image($request['voucher_image'], '/student/', $application->professional_id . '-' . $request['id'] . '-transcript_image');
            } else {
                $voucher_image = ($request['voucher_image']) ? $request['voucher_image'] : null;
            }
            $application->exam_id = $request['exam_id'];
            $application->level_id = $request['level'];
            $application->program_id = $request['program'];
            $application->voucher_image = $voucher_image;
            $application->save();
            $response['data'] = null;
            $response['message'] = 'Application updated successfully';
            $response['error'] = null;
            $response['status'] = 200;
            return response()->json($response, $response['status']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function delete($request)
    {
        try {
            $id = $request->id;
            Applicant::where('id', $id)->delete();
            $response['message'] = 'Applicant deleted successfully.';
            $response['error'] = null;
            $response['status'] = 200;
            return response()->json($response, $response['status']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
    public function saveStatus($request)
    {
        try {
            $admin = auth()->guard('admin')->user();
            $admin_state = Str::slug($admin->role->name, '_');
            $applicant = Applicant::with('professional:id,name,email')->where('id', $request['id'])->first();
            if ($applicant) {
                if ($request['status'] === "accepted") {
                    $next_state = VerificationStep::with('role')->where(
                        'step',
                        '>',
                        VerificationStep::where('role_id', $admin->role_id)->select('step')
                    )->first();
                    if ($next_state) {
                        $status = 'progress';
                        $state = Str::slug($next_state->role->name, '_');
                        $log_default = 'Profile has been verified and forwarded to ' . $next_state->role->name;
                    } else {
                        // Final Step (No next state)
                        $status = 'accepted';
                        $state = $admin_state;
                        $log_default = 'Application has been finally accepted by ' . $admin->role->name;
                    }

                    $default = 'Application has been accepted';

                    $remarks = !empty($request['remarks'])
                        ? $request['remarks']
                        : $default;

                    $applicant_log_remarks = !empty($request['remarks'])
                        ? $request['remarks']
                        : $log_default;

                    $subject = 'Application Form Approval Notification';

                    $this->updateStateApplicantLog($applicant->professional_id, $applicant->id, $request['status'], $admin_state, $remarks); // for own state
                    $this->updateStateApplicantLog($applicant->professional_id, $applicant->id, $status, $state, $applicant_log_remarks);    // for next state

                } elseif ($request['status'] === "rejected") {
                    $status = 'rejected';
                    $state = $admin_state;
                    $default = 'Rejected By ' . $admin->role->name;
                    $log_default = 'Rejected By ' . $admin->role->name;

                    $remarks = !empty($request['remarks'])
                        ? $request['remarks']
                        : $default;

                    $applicant_log_remarks = !empty($request['remarks'])
                        ? $request['remarks']
                        : $log_default;

                    $subject = 'Application Form Status Update';

                    $this->updateStateApplicantLog($applicant->professional_id, $applicant->id, $request['status'], $admin_state, $remarks); // for own state

                } elseif ($request['status'] === "onhold") {
                    $status = 'onhold';
                    $state = $admin_state;
                    $default = 'Hold By ' . $admin->role->name;
                    $log_default = 'Hold By ' . $admin->role->name;

                    $remarks = !empty($request['remarks'])
                        ? $request['remarks']
                        : $default;

                    $applicant_log_remarks = !empty($request['remarks'])
                        ? $request['remarks']
                        : $log_default;

                    $subject = null;

                    $this->updateStateApplicantLog($applicant->professional_id, $applicant->id, $request['status'], $admin_state, $remarks); // for own state

                } else {
                    $status = 'pending';
                    $state = $admin_state;
                    $default = 'Pending By ' . $admin->role->name;
                    $log_default = 'Pending By ' . $admin->role->name;

                    $remarks = !empty($request['remarks'])
                        ? $request['remarks']
                        : $default;

                    $applicant_log_remarks = !empty($request['remarks'])
                        ? $request['remarks']
                        : $log_default;

                    $subject = null;

                    $this->updateStateApplicantLog($applicant->professional_id, $applicant->id, $request['status'], $admin_state, $remarks); // for own state
                }

                $applicant->rejected = $request['status'] === "rejected" ? 1 : 0;
                $applicant->status = $status;
                $applicant->state = $state;
                $applicant->remarks = $remarks;
                $applicant->save();

                if ($request['status'] === "accepted" || $request['status'] === "rejected") {
                    $email_data = [
                        'name' => $applicant->professional->name,
                        'email' => $applicant->professional->email,
                        'status' => $request['status'],
                        'subject' => $subject,
                        'remarks' => $remarks,
                    ];
                    $jobToDispatch = (new ApplicantStatusJob($email_data))->delay(Carbon::now()->addSeconds(1));
                    dispatch($jobToDispatch);
                }
                $response['message'] = 'Status updated successfully.';
                $response['error'] = null;
                $response['status'] = 201;
                return response()->json($response, $response['status']);
            }

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function updateStateApplicantLog($professional_id, $applicant_id, $status, $state, $remarks)
    {
        $exam_log = new ApplicantLog();
        $exam_log->professional_id = $professional_id;
        $exam_log->admin_id = auth()->guard('admin')->id();
        $exam_log->applicant_id = $applicant_id;
        $exam_log->remarks = $remarks;
        $exam_log->state = $state;
        $exam_log->status = $status;
        $exam_log->save();
    }

    public function saveState($request)
    {
        try {
            $status = 'progress';
            $state = $request['state'];
            $remarks = 'Exam Applied has been forwarded to ' . ucwords(strtolower(str_replace('_', ' ', $request['state'])));
            $applicant_log_remarks = 'Profile Verified and forwarded to ' . ucwords(strtolower(str_replace('_', ' ', $request['state'])));
            $subject = 'Application Form Forwarded Notification';

            $applicant = Applicant::where('id', $request['id'])->first();
            $applicant->status = $status;
            $applicant->state = $state;
            $applicant->remarks = $remarks;
            $applicant->save();

            $logData = [
                'professional_id' => $applicant->professional_id,
                'admin_id' => auth()->guard('admin')->id(),
                'applicant_id' => $applicant->id,
                'remarks' => $applicant_log_remarks,
                'state' => $state,
                'status' => $status,
            ];
            $this->applicantLog($logData);

            $email_data = [
                'name' => $applicant->professional->name,
                'email' => $applicant->professional->email,
                'state' => $state,
                'status' => $status,
                'subject' => $subject,
                'remarks' => $remarks,
            ];
            $jobToDispatch = (new ApplicantStatusJob($email_data))->delay(now()->addSeconds(1));
            if ($status == 'accepted' || $status == 'progress' || $status == 'rejected') {
                dispatch($jobToDispatch);
            }

            $response['message'] = 'File forwarded to ' . $request['state'] . ' successfully.';
            $response['error'] = null;
            $response['status'] = 201;
            return response()->json($response, $response['status']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function applicantLog($logData)
    {
        $applicant_log = new ApplicantLog();
        $applicant_log->professional_id = $logData['professional_id'];
        $applicant_log->admin_id = $logData['admin_id'];
        $applicant_log->applicant_id = $logData['applicant_id'];
        $applicant_log->remarks = $logData['remarks'];
        $applicant_log->state = $logData['state'];
        $applicant_log->status = $logData['status'];
        $applicant_log->save();
    }
}