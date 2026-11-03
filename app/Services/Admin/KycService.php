<?php
namespace App\Services\Admin;

use App\Models\Certificate;
use App\Models\Kyc;
use Yajra\DataTables\Facades\DataTables;

class KycService {
    public function list() {
        try {
            $data = Kyc::select('*');
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('profile_image', function ($row) {
                    $html = '<a href="' . $row->full_profile_img . '" data-fancybox="image"><img style="border-radius:50px; height: 45px; width:45px"
                                                                src="' . $row->full_profile_img . '"></a>';
                    return $html;
                })
                ->addColumn('action', function ($row) {
                    $btn = '';
                    $detailUrl = route('admin.professional.detail', ['id' => $row->professional_id]);
                    $btn .= '<a href="' . $detailUrl . '"><span
                    class="mat_sym-icon material-symbols-outlined"><i class="mdi mdi-eye-outline"></i></span></a>';
                    if (auth()->guard('admin')->user()->hasPermission('Kyc', 'delete')) {
                        $btn .= ' <a href="javascript:void(0)" onclick="deleteData(' . $row->id . ')"><span class="mat_sym-icon material-symbols-outlined"><i class="mdi mdi-delete-forever-outline"></i> </span></a>';
                    }
                    return $btn;
                })
                ->rawColumns(['profile_image', 'action'])
                ->make(true);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
    public function delete($request) {
        try {
            $id = $request->id;
            Kyc::where('id', $id)->delete();
            $response['message'] = 'Kyc deleted successfully.';
            $response['error'] = null;
            $response['status'] = 200;
            return response()->json($response, $response['status']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function certificate($is_printed , $level_id,  $program_id , $decision_date) {
        try {
            $data = Certificate::with(['professional', 'professional.info', 'level:id,name', 'program:id,name']);
            if ($level_id) {
                $data->where('certificates.level_id', $level_id);
            }
            if ($program_id) {
                $data->where('certificates.program_id', $program_id);
            }
            if ($decision_date) {
                $data->where('certificates.decision_date', $decision_date);
            }
            if ($is_printed) {
                $is_printed = $is_printed == 'Printed' ? 1 : 0;
                $data->where('certificates.is_printed', $is_printed);
            }
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('name', function ($row) {
                    return  ucwords(strtolower($row->name));
                })
                ->addColumn('professional_name', function ($row) {
                    return  ucwords(strtolower(optional($row->professional)->name));
                })
                ->addColumn('profile_image', function ($row) {
                    $url = optional($row->professional?->info)->full_profile_picture;
                    $html = '<a href="' . $url . '" data-fancybox="image"><img style="border-radius:50px; height: 45px; width:45px"
                                                                src="' . $url . '"></a>';
                    return $html;
                })
                ->addColumn('citizenship_number', function ($row) {
                    return optional($row->professional?->info)->citizenship_number;
                })
                ->addColumn('level_name', function ($row) {
                    return  optional( $row->level)->name;
                })
                ->addColumn('program_name', function ($row) {
                    return optional($row->program)->name;
                })
                ->addColumn('status', function ($row) {
                    $colors = ['1' => '#008000', '0' => '#ff0000'];
                    $color = $colors[$row->is_printed] ?? '#808080';
                    $status = $row->is_printed == 1 ? 'PRINTED' : 'NOT PRINTED';
                    return '<span style="
                        background-color:' . $color . ';
                        color:#fff;
                        padding:3px;
                        border-radius:4px;
                    ">' .  $status . '</span>';
                })
                ->addColumn('action', function ($row) {
                    $btn = '';
                    if(auth()->guard('admin')->user()->hasPermission('Certificates', 'edit')) {
                        $btn .= '<button data-bs-toggle="modal" data-bs-target="#allocateModal"  data-id="'.$row->id.'"><span 
                        class="mat_sym-icon material-symbols-outlined"><i class="mdi mdi-anchor"></i></span></button>';
                    }
                    return $btn;
                })
                ->rawColumns(['name', 'profile_image', 'status', 'action'])
                ->make(true);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }


    public function saveAllocate($request) {
        try {
            $certificate = Certificate::where('id', $request['id'])->first();
            $certificate->professional_id = $request['professional_id'];
            $certificate->save();
            $response['message'] = 'Allocate successfully.';
            $response['error'] = null;
            $response['status'] = 200;
            return response()->json($response, $response['status']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

}