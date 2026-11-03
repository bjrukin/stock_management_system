<?php
namespace App\Services\Admin;

use App\Models\Professional;
use App\Models\ProfessionalInfo;
use App\Models\ProfessionalQualification;
use App\Traits\StoreImageTrait;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class ProfessionalService {
    use StoreImageTrait;

    function list() {
        try {
            $data = Professional::with('info:professional_id,citizenship_number')->select('*');
            return DataTables::of($data)
                ->filterColumn('citizenship_number', function ($query, $keyword) {
                    $query->whereHas('info', function ($qry) use ($keyword) {
                        $qry->where('citizenship_number', 'LIKE', '%' . $keyword . '%');
                    });
                })
                ->addIndexColumn()
                ->addColumn('citizenship_number', function ($row) {
                    return $row->info->citizenship_number ?? null;
                })
                ->addColumn('status', function ($row) {
                    $checked = $row->status == 1 ? 'checked' : '';
                    $html = '<label class="switch">
                                <input type="checkbox" class="switch-input switch-status"
                                    data-id="' . $row->id . '"
                                    data-status-value="' . $row->status . '" ' . $checked . '>
                                <span class="switch-label" data-on="Show" data-off="Hide"></span>
                                <span class="switch-handle"></span>
                            </label>';
                    return $html;
                })
                ->addColumn('action', function ($row) {
                    $detailUrl = route('admin.professional.detail', ['id' => $row->id]);
                    $btn = '<a href="' . $detailUrl . '"><span
                    class="mat_sym-icon material-symbols-outlined"><i class="mdi mdi-eye-outline"></i></span></a>';
                    if (auth()->guard('admin')->user()->hasPermission('Professional', 'edit')) {
                        $editUrl = route('admin.professional.profile', ['id' => $row->id]);
                        $btn .= '<a href="' . $editUrl . '"><span
                    class="mat_sym-icon material-symbols-outlined"><i class="mdi mdi-pencil-outline"></i></span></a>';
                    }
                    if (auth()->guard('admin')->user()->hasPermission('Professional', 'delete')) {
                        $btn .= ' <a href="javascript:void(0)" onclick="deleteData(' . $row->id . ')"><span class="mat_sym-icon material-symbols-outlined"><i class="mdi mdi-delete-forever-outline"></i></span></a>';
                    }
                    return $btn;
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function status($request) {
        try {
            Professional::where('id', $request->id)
                ->update([
                    $request->field_name => $request->val,
                ]);
            if ($request->val == 1) {
                $response['message'] = 'Professional enabled successfully';
            } else {
                $response['message'] = 'Professional disabled successfully';
            }
            $response['error'] = null;
            $response['status'] = 200;
            return response()->json($response, $response['status']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function delete($request) {
        try {
            $id = $request->id;
            Professional::where('id', $id)->delete();
            $response['message'] = 'Professional deleted successfully.';
            $response['error'] = null;
            $response['status'] = 200;
            return response()->json($response, $response['status']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function imageProfileDelete($request) {
        try {
            $id = $request->id;
            $field_name = $request->field_name;
            $row = ProfessionalInfo::where('professional_id', $id)->first();
            if ($row) {
                Storage::disk('public')->delete('/student/' . $row->$field_name);
                $row->$field_name = '';
                $row->save();
            }
            $response['message'] = 'Image deleted successfully.';
            $response['error'] = null;
            $response['status'] = 200;
            return response()->json($response, $response['status']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function saveProfile($request) {
        try {
            $professional_info = ProfessionalInfo::where('professional_id', $request['id'])->first();
            if (preg_match('#^data:image.*?base64,#', $request['profile_picture'])) {
                $profile_picture = $this->StoreBase64Image($request['profile_picture'], '/student/', $request['id'] . '-' . time() . '-profile_picture');
            } else {
                $profile_picture = ($request['profile_picture']) ? $request['profile_picture'] : null;
            }
            if (preg_match('#^data:image.*?base64,#', $request['citizenship_front'])) {
                $citizenship_front = $this->StoreBase64Image($request['citizenship_front'], '/student/', $request['id'] . '-' . time() . '-citizenship_front');
            } else {
                $citizenship_front = $request['citizenship_front'] ?? null;
            }
            if (preg_match('#^data:image.*?base64,#', $request['citizenship_back'])) {
                $citizenship_back = $this->StoreBase64Image($request['citizenship_back'], '/student/', $request['id'] . '-' . time() . '-citizenship_back');
            } else {
                $citizenship_back = $request['citizenship_back'] ?? null;
            }
            if (preg_match('#^data:image.*?base64,#', $request['signature_image'])) {
                $signature_image = $this->StoreBase64Image($request['signature_image'], '/student/', $request['id'] . '-' . time() . '-signature_image');
            } else {
                $signature_image = $request['signature_image'] ?? null;
            }
            $professional_info->profile_picture = $profile_picture;
            $professional_info->citizenship_front = $citizenship_front;
            $professional_info->citizenship_back = $citizenship_back;
            $professional_info->signature_image = $signature_image;
            $professional_info->first_name = $request['first_name'];
            $professional_info->middle_name = $request['middle_name'];
            $professional_info->last_name = $request['last_name'];
            $professional_info->first_name_nep = $request['first_name_nep'];
            $professional_info->middle_name_nep = $request['middle_name_nep'];
            $professional_info->last_name_nep = $request['last_name_nep'];
            $professional_info->dob_eng = $request['dob_eng'];
            $professional_info->dob_nep = $request['dob_nep'];
            $professional_info->gender = $request['gender'];
            $professional_info->marital_status = $request['marital_status'];
            $professional_info->ethinic = $request['ethinic'];
            $professional_info->citizenship_number = $request['citizenship_number'];
            $professional_info->citizenship_issue_date = $request['citizenship_issue_date'];
            $professional_info->citizenship_issue_district = $request['citizenship_issue_district'];
            $professional_info->province_id = $request['province'];
            $professional_info->district_id = $request['district'];
            $professional_info->municipality_id = $request['municipality'];
            $professional_info->ward_no = $request['ward_no'];
            $professional_info->level_id = $request['level'];
            $professional_info->save();
            $response['data'] = $professional_info;
            $response['message'] = 'Profile updated successfully';
            $response['error'] = null;
            $response['status'] = 200;
            return response()->json($response, $response['status']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function imageQualificationDelete($request) {
        try {
            $id = $request->id;
            $field_name = $request->field_name;
            $row = ProfessionalQualification::where('id', $id)->first();
            if ($row) {
                Storage::disk('public')->delete('/student/' . $row->$field_name);
                $row->$field_name = '';
                $row->save();
            }
            $response['message'] = 'Image deleted successfully.';
            $response['error'] = null;
            $response['status'] = 200;
            return response()->json($response, $response['status']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function saveSlc($request) {
        try {
            $professional_qualification = ProfessionalQualification::where('id', $request['id'])->first();
            if (preg_match('#^data:image.*?base64,#', $request['transcript_image'])) {
                $transcript_image = $this->StoreBase64Image($request['transcript_image'], '/student/', $professional_qualification->professional_id . '-'  . time() . '-transcript_image');
            } else {
                $transcript_image = ($request['transcript_image']) ? $request['transcript_image'] : null;
            }
            if (preg_match('#^data:image.*?base64,#', $request['transcript_bac_1'])) {
                $transcript_bac_1 = $this->StoreBase64Image($request['transcript_bac_1'], '/student/', $professional_qualification->professional_id . '-'  . time() . '-transcript_bac_1');
            } else {
                $transcript_bac_1 = ($request['transcript_bac_1']) ? $request['transcript_bac_1'] : null;
            }
            if (preg_match('#^data:image.*?base64,#', $request['provisional_image'])) {
                $provisional_image = $this->StoreBase64Image($request['provisional_image'], '/student/', $professional_qualification->professional_id . '-'  . time() . '-provisional_image');
            } else {
                $provisional_image = $request['provisional_image'] ?? null;
            }
            if (preg_match('#^data:image.*?base64,#', $request['character_image'])) {
                $character_image = $this->StoreBase64Image($request['character_image'], '/student/', $professional_qualification->professional_id . '-'  . time() . '-character_image');
            } else {
                $character_image = $request['character_image'] ?? null;
            }
            if (preg_match('#^data:image.*?base64,#', $request['equivalence_certificate'])) {
                $equivalence_certificate = $this->StoreBase64Image($request['equivalence_certificate'], '/student/', $professional_qualification->professional_id . '-'  . time() . '-equivalence_certificate');
            } else {
                $equivalence_certificate = $request['equivalence_certificate'] ?? null;
            }

            $professional_qualification->college_name = $request['school_name'];
            $professional_qualification->passed_year = $request['passed_year'];
            $professional_qualification->board_university = $request['board_university'];
            $professional_qualification->transcript_image = $transcript_image;
            $professional_qualification->transcript_bac_1 = $transcript_bac_1;
            $professional_qualification->provisional_image = $provisional_image;
            $professional_qualification->character_image = $character_image;
            $professional_qualification->equivalence_certificate = $equivalence_certificate;
            $professional_qualification->save();
            $response['data'] = null;
            $response['message'] = 'SLC updated successfully';
            $response['error'] = null;
            $response['status'] = 200;
            return response()->json($response, $response['status']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
    public function saveTslc($request) {
        try {
            $professional_qualification = ProfessionalQualification::where('id', $request['id'])->first();
            if (preg_match('#^data:image.*?base64,#', $request['transcript_image'])) {
                $transcript_image = $this->StoreBase64Image($request['transcript_image'], '/student/', $professional_qualification->professional_id . '-'  . time() . '-transcript_image');
            } else {
                $transcript_image = ($request['transcript_image']) ? $request['transcript_image'] : null;
            }
            if (preg_match('#^data:image.*?base64,#', $request['transcript_bac_1'])) {
                $transcript_bac_1 = $this->StoreBase64Image($request['transcript_bac_1'], '/student/', $professional_qualification->professional_id . '-'  . time() . '-transcript_image');
            } else {
                $transcript_bac_1 = ($request['transcript_bac_1']) ? $request['transcript_bac_1'] : null;
            }
            if (preg_match('#^data:image.*?base64,#', $request['transcript_bac_2'])) {
                $transcript_bac_2 = $this->StoreBase64Image($request['transcript_bac_2'], '/student/', $professional_qualification->professional_id . '-'  . time() . '-transcript_image');
            } else {
                $transcript_bac_2 = ($request['transcript_bac_2']) ? $request['transcript_bac_2'] : null;
            }
            if (preg_match('#^data:image.*?base64,#', $request['transcript_bac_3'])) {
                $transcript_bac_3 = $this->StoreBase64Image($request['transcript_bac_3'], '/student/', $professional_qualification->professional_id . '-'  . time() . '-transcript_image');
            } else {
                $transcript_bac_3 = ($request['transcript_bac_3']) ? $request['transcript_bac_3'] : null;
            }
            if (preg_match('#^data:image.*?base64,#', $request['provisional_image'])) {
                $provisional_image = $this->StoreBase64Image($request['provisional_image'], '/student/', $professional_qualification->professional_id . '-'  . time() . '-transcript_image');
            } else {
                $provisional_image = $request['provisional_image'] ?? null;
            }
            if (preg_match('#^data:image.*?base64,#', $request['character_image'])) {
                $character_image = $this->StoreBase64Image($request['character_image'], '/student/', $professional_qualification->professional_id . '-'  . time() . '-transcript_image');
            } else {
                $character_image = $request['character_image'] ?? null;
            }
            if (preg_match('#^data:image.*?base64,#', $request['ojt_image'])) {
                $ojt_image = $this->StoreBase64Image($request['ojt_image'], '/student/', $professional_qualification->professional_id . '-'  . time() . '-transcript_image');
            } else {
                $ojt_image = $request['ojt_image'] ?? null;
            }

            $professional_qualification->college_name = ['college_name'];
            $professional_qualification->passed_year = $request['passed_year'];
            $professional_qualification->board_university = $request['board_university'];
            $professional_qualification->registration_number = $request['registration_number'];
            $professional_qualification->transcript_image = $transcript_image;
            $professional_qualification->transcript_bac_1 = $transcript_bac_1;
            $professional_qualification->transcript_bac_2 = $transcript_bac_2;
            $professional_qualification->transcript_bac_3 = $transcript_bac_3;
            $professional_qualification->provisional_image = $provisional_image;
            $professional_qualification->character_image = $character_image;
            $professional_qualification->ojt_image = $ojt_image;
            $professional_qualification->save();
            $response['data'] = null;
            $response['message'] = 'TSLC updated successfully';
            $response['error'] = null;
            $response['status'] = 200;
            return response()->json($response, $response['status']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function savePcl($request) {
        try {
            $professional_qualification = ProfessionalQualification::where('id', $request['id'])->first();
            if (preg_match('#^data:image.*?base64,#', $request['transcript_image'])) {
                $transcript_image = $this->StoreBase64Image($request['transcript_image'], '/student/', $professional_qualification->professional_id . '-'  . time() . '-transcript_image');
            } else {
                $transcript_image = ($request['transcript_image']) ? $request['transcript_image'] : null;
            }
            if (preg_match('#^data:image.*?base64,#', $request['transcript_bac_1'])) {
                $transcript_bac_1 = $this->StoreBase64Image($request['transcript_bac_1'], '/student/', $professional_qualification->professional_id . '-'  . time() . '-transcript_image');
            } else {
                $transcript_bac_1 = ($request['transcript_bac_1']) ? $request['transcript_bac_1'] : null;
            }
            if (preg_match('#^data:image.*?base64,#', $request['provisional_image'])) {
                $provisional_image = $this->StoreBase64Image($request['provisional_image'], '/student/', $professional_qualification->professional_id . '-'  . time() . '-transcript_image');
            } else {
                $provisional_image = $request['provisional_image'] ?? null;
            }
            if (preg_match('#^data:image.*?base64,#', $request['character_image'])) {
                $character_image = $this->StoreBase64Image($request['character_image'], '/student/', $professional_qualification->professional_id . '-'  . time() . '-transcript_image');
            } else {
                $character_image = $request['character_image'] ?? null;
            }
            if (preg_match('#^data:image.*?base64,#', $request['equivalence_certificate'])) {
                $equivalence_certificate = $this->StoreBase64Image($request['equivalence_certificate'], '/student/', $professional_qualification->professional_id . '-'  . time() . '-transcript_image');
            } else {
                $equivalence_certificate = $request['equivalence_certificate'] ?? null;
            }
            if (preg_match('#^data:image.*?base64,#', $request['equivalence_certificate'])) {
                $equivalence_certificate = $this->StoreBase64Image($request['equivalence_certificate'], '/student/', $professional_qualification->professional_id . '-'  . time() . '-transcript_image');
            } else {
                $equivalence_certificate = $request['equivalence_certificate'] ?? null;
            }
            if (preg_match('#^data:image.*?base64,#', $request['council_registration_certificate'])) {
                $council_registration_certificate = $this->StoreBase64Image($request['council_registration_certificate'], '/student/', $professional_qualification->professional_id . '-'  . time() . '-transcript_image');
            } else {
                $council_registration_certificate = $request['council_registration_certificate'] ?? null;
            }
            if (preg_match('#^data:image.*?base64,#', $request['ojt_pcl_community_1_image'])) {
                $ojt_pcl_community_1_image = $this->StoreBase64Image($request['ojt_pcl_community_1_image'], '/student/', $professional_qualification->professional_id . '-'  . time() . '-transcript_image');
            } else {
                $ojt_pcl_community_1_image = $request['ojt_pcl_community_1_image'] ?? null;
            }
            if (preg_match('#^data:image.*?base64,#', $request['ojt_pcl_community_2_image'])) {
                $ojt_pcl_community_2_image = $this->StoreBase64Image($request['ojt_pcl_community_2_image'], '/student/', $professional_qualification->professional_id . '-'  . time() . '-transcript_image');
            } else {
                $ojt_pcl_community_2_image = $request['ojt_pcl_community_2_image'] ?? null;
            }
            if (preg_match('#^data:image.*?base64,#', $request['noc_image'])) {
                $noc_image = $this->StoreBase64Image($request['noc_image'], '/student/', $professional_qualification->professional_id . '-'  . time() . '-transcript_image');
            } else {
                $noc_image = $request['noc_image'] ?? null;
            }

            $professional_qualification->college_name = $request['college_name'];
            $professional_qualification->admission_year = $request['admission_year'];
            $professional_qualification->passed_year = $request['passed_year'];
            $professional_qualification->board_university = $request['board_university'];
            $professional_qualification->registration_number = $request['registration_number'];
            $professional_qualification->transcript_image = $transcript_image;
            $professional_qualification->transcript_bac_1 = $transcript_bac_1;
            $professional_qualification->provisional_image = $provisional_image;
            $professional_qualification->character_image = $character_image;
            $professional_qualification->equivalence_certificate = $equivalence_certificate;
            $professional_qualification->council_registration_certificate = $council_registration_certificate;
            $professional_qualification->ojt_pcl_community_1_image = $ojt_pcl_community_1_image;
            $professional_qualification->ojt_pcl_community_2_image = $ojt_pcl_community_2_image;
            $professional_qualification->noc_image = $noc_image;
            $professional_qualification->save();
            $response['data'] = null;
            $response['message'] = 'PCL updated successfully';
            $response['error'] = null;
            $response['status'] = 200;
            return response()->json($response, $response['status']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function saveBachelor($request) {
        try {
            $professional_qualification = ProfessionalQualification::where('id', $request['id'])->first();
            if (preg_match('#^data:image.*?base64,#', $request['transcript_bac_1'])) {
                $transcript_bac_1 = $this->StoreBase64Image($request['transcript_bac_1'], '/student/', $professional_qualification->professional_id . '-'  . time() . '-transcript_image');
            } else {
                $transcript_bac_1 = ($request['transcript_bac_1']) ? $request['transcript_bac_1'] : null;
            }
            if (preg_match('#^data:image.*?base64,#', $request['transcript_bac_2'])) {
                $transcript_bac_2 = $this->StoreBase64Image($request['transcript_bac_2'], '/student/', $professional_qualification->professional_id . '-'  . time() . '-transcript_image');
            } else {
                $transcript_bac_2 = ($request['transcript_bac_2']) ? $request['transcript_bac_2'] : null;
            }
            if (preg_match('#^data:image.*?base64,#', $request['transcript_bac_3'])) {
                $transcript_bac_3 = $this->StoreBase64Image($request['transcript_bac_3'], '/student/', $professional_qualification->professional_id . '-'  . time() . '-transcript_image');
            } else {
                $transcript_bac_3 = ($request['transcript_bac_3']) ? $request['transcript_bac_3'] : null;
            }
            if (preg_match('#^data:image.*?base64,#', $request['transcript_bac_4'])) {
                $transcript_bac_4 = $this->StoreBase64Image($request['transcript_bac_4'], '/student/', $professional_qualification->professional_id . '-'  . time() . '-transcript_image');
            } else {
                $transcript_bac_4 = ($request['transcript_bac_4']) ? $request['transcript_bac_4'] : null;
            }
            if (preg_match('#^data:image.*?base64,#', $request['transcript_bac_5'])) {
                $transcript_bac_5 = $this->StoreBase64Image($request['transcript_bac_5'], '/student/', $professional_qualification->professional_id . '-'  . time() . '-transcript_image');
            } else {
                $transcript_bac_5 = ($request['transcript_bac_5']) ? $request['transcript_bac_5'] : null;
            }
            if (preg_match('#^data:image.*?base64,#', $request['transcript_bac_6'])) {
                $transcript_bac_6 = $this->StoreBase64Image($request['transcript_bac_6'], '/student/', $professional_qualification->professional_id . '-'  . time() . '-transcript_image');
            } else {
                $transcript_bac_6 = ($request['transcript_bac_6']) ? $request['transcript_bac_6'] : null;
            }
            if (preg_match('#^data:image.*?base64,#', $request['transcript_bac_7'])) {
                $transcript_bac_7 = $this->StoreBase64Image($request['transcript_bac_7'], '/student/', $professional_qualification->professional_id . '-'  . time() . '-transcript_image');
            } else {
                $transcript_bac_7 = ($request['transcript_bac_7']) ? $request['transcript_bac_7'] : null;
            }
            if (preg_match('#^data:image.*?base64,#', $request['transcript_bac_8'])) {
                $transcript_bac_8 = $this->StoreBase64Image($request['transcript_bac_8'], '/student/', $professional_qualification->professional_id . '-'  . time() . '-transcript_image');
            } else {
                $transcript_bac_8 = ($request['transcript_bac_8']) ? $request['transcript_bac_8'] : null;
            }
            if (preg_match('#^data:image.*?base64,#', $request['equivalence_certificate'])) {
                $equivalence_certificate = $this->StoreBase64Image($request['equivalence_certificate'], '/student/', $professional_qualification->professional_id . '-'  . time() . '-transcript_image');
            } else {
                $equivalence_certificate = $request['equivalence_certificate'] ?? null;
            }
            if (preg_match('#^data:image.*?base64,#', $request['provisional_image'])) {
                $provisional_image = $this->StoreBase64Image($request['provisional_image'], '/student/', $professional_qualification->professional_id . '-'  . time() . '-transcript_image');
            } else {
                $provisional_image = $request['provisional_image'] ?? null;
            }
            if (preg_match('#^data:image.*?base64,#', $request['character_image'])) {
                $character_image = $this->StoreBase64Image($request['character_image'], '/student/', $professional_qualification->professional_id . '-'  . time() . '-transcript_image');
            } else {
                $character_image = $request['character_image'] ?? null;
            }
            if (preg_match('#^data:image.*?base64,#', $request['intership_image'])) {
                $intership_image = $this->StoreBase64Image($request['intership_image'], '/student/', $professional_qualification->professional_id . '-'  . time() . '-transcript_image');
            } else {
                $intership_image = $request['intership_image'] ?? null;
            }
            if (preg_match('#^data:image.*?base64,#', $request['noc_image'])) {
                $noc_image = $this->StoreBase64Image($request['noc_image'], '/student/', $professional_qualification->professional_id . '-'  . time() . '-transcript_image');
            } else {
                $noc_image = $request['noc_image'] ?? null;
            }
            if (preg_match('#^data:image.*?base64,#', $request['visa_image'])) {
                $visa_image = $this->StoreBase64Image($request['visa_image'], '/student/', $professional_qualification->professional_id . '-'  . time() . '-transcript_image');
            } else {
                $visa_image = $request['visa_image'] ?? null;
            }
            if (preg_match('#^data:image.*?base64,#', $request['passport_image'])) {
                $passport_image = $this->StoreBase64Image($request['passport_image'], '/student/', $professional_qualification->professional_id . '-'  . time() . '-transcript_image');
            } else {
                $passport_image = $request['passport_image'] ?? null;
            }

            $professional_qualification->college_name = $request['college_name'];
            $professional_qualification->admission_year = $request['admission_year'];
            $professional_qualification->passed_year = $request['passed_year'];
            $professional_qualification->board_university = $request['board_university'];
            $professional_qualification->registration_number = $request['registration_number'];
            $professional_qualification->transcript_bac_1 = $transcript_bac_1;
            $professional_qualification->transcript_bac_2 = $transcript_bac_2;
            $professional_qualification->transcript_bac_3 = $transcript_bac_3;
            $professional_qualification->transcript_bac_4 = $transcript_bac_4;
            $professional_qualification->transcript_bac_5 = $transcript_bac_5;
            $professional_qualification->transcript_bac_6 = $transcript_bac_6;
            $professional_qualification->transcript_bac_7 = $transcript_bac_7;
            $professional_qualification->transcript_bac_8 = $transcript_bac_8;
            $professional_qualification->equivalence_certificate = $equivalence_certificate;
            $professional_qualification->provisional_image = $provisional_image;
            $professional_qualification->character_image = $character_image;
            $professional_qualification->intership_image = $intership_image;
            $professional_qualification->noc_image = $noc_image;
            $professional_qualification->visa_image = $visa_image;
            $professional_qualification->passport_image = $passport_image;
            $professional_qualification->save();
            $response['data'] = null;
            $response['message'] = 'Bachelor updated successfully';
            $response['error'] = null;
            $response['status'] = 200;
            return response()->json($response, $response['status']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function saveMaster($request) {
        try {
            $professional_qualification = ProfessionalQualification::where('id', $request['id'])->first();
            if (preg_match('#^data:image.*?base64,#', $request['transcript_mas_marksheet'])) {
                $transcript_mas_marksheet = $this->StoreBase64Image($request['transcript_mas_marksheet'], '/student/', $professional_qualification->professional_id . '-'  . time() . '-transcript_image');
            } else {
                $transcript_mas_marksheet = ($request['transcript_mas_marksheet']) ? $request['transcript_mas_marksheet'] : null;
            }
            if (preg_match('#^data:image.*?base64,#', $request['transcript_bac_1'])) {
                $transcript_bac_1 = $this->StoreBase64Image($request['transcript_bac_1'], '/student/', $professional_qualification->professional_id . '-'  . time() . '-transcript_image');
            } else {
                $transcript_bac_1 = ($request['transcript_bac_1']) ? $request['transcript_bac_1'] : null;
            }
            if (preg_match('#^data:image.*?base64,#', $request['transcript_bac_2'])) {
                $transcript_bac_2 = $this->StoreBase64Image($request['transcript_bac_2'], '/student/', $professional_qualification->professional_id . '-'  . time() . '-transcript_image');
            } else {
                $transcript_bac_2 = ($request['transcript_bac_2']) ? $request['transcript_bac_2'] : null;
            }
            if (preg_match('#^data:image.*?base64,#', $request['transcript_bac_3'])) {
                $transcript_bac_3 = $this->StoreBase64Image($request['transcript_bac_3'], '/student/', $professional_qualification->professional_id . '-'  . time() . '-transcript_image');
            } else {
                $transcript_bac_3 = ($request['transcript_bac_3']) ? $request['transcript_bac_3'] : null;
            }
            if (preg_match('#^data:image.*?base64,#', $request['equivalence_certificate'])) {
                $equivalence_certificate = $this->StoreBase64Image($request['equivalence_certificate'], '/student/', $professional_qualification->professional_id . '-'  . time() . '-transcript_image');
            } else {
                $equivalence_certificate = $request['equivalence_certificate'] ?? null;
            }
            if (preg_match('#^data:image.*?base64,#', $request['provisional_image'])) {
                $provisional_image = $this->StoreBase64Image($request['provisional_image'], '/student/', $professional_qualification->professional_id . '-'  . time() . '-transcript_image');
            } else {
                $provisional_image = $request['provisional_image'] ?? null;
            }
            if (preg_match('#^data:image.*?base64,#', $request['character_image'])) {
                $character_image = $this->StoreBase64Image($request['character_image'], '/student/', $professional_qualification->professional_id . '-'  . time() . '-transcript_image');
            } else {
                $character_image = $request['character_image'] ?? null;
            }
            if (preg_match('#^data:image.*?base64,#', $request['intership_image'])) {
                $intership_image = $this->StoreBase64Image($request['intership_image'], '/student/', $professional_qualification->professional_id . '-'  . time() . '-transcript_image');
            } else {
                $intership_image = $request['intership_image'] ?? null;
            }
            if (preg_match('#^data:image.*?base64,#', $request['noc_image'])) {
                $noc_image = $this->StoreBase64Image($request['noc_image'], '/student/', $professional_qualification->professional_id . '-'  . time() . '-transcript_image');
            } else {
                $noc_image = $request['noc_image'] ?? null;
            }
            if (preg_match('#^data:image.*?base64,#', $request['visa_image'])) {
                $visa_image = $this->StoreBase64Image($request['visa_image'], '/student/', $professional_qualification->professional_id . '-'  . time() . '-transcript_image');
            } else {
                $visa_image = $request['visa_image'] ?? null;
            }
            if (preg_match('#^data:image.*?base64,#', $request['passport_image'])) {
                $passport_image = $this->StoreBase64Image($request['passport_image'], '/student/', $professional_qualification->professional_id . '-'  . time() . '-transcript_image');
            } else {
                $passport_image = $request['passport_image'] ?? null;
            }

            $professional_qualification->college_name = $request['college_name'];
            $professional_qualification->admission_year = $request['admission_year'];
            $professional_qualification->passed_year = $request['passed_year'];
            $professional_qualification->board_university = $request['board_university'];
            $professional_qualification->registration_number = $request['registration_number'];
            $professional_qualification->transcript_mas_marksheet = $transcript_mas_marksheet;
            $professional_qualification->transcript_bac_1 = $transcript_bac_1;
            $professional_qualification->transcript_bac_2 = $transcript_bac_2;
            $professional_qualification->transcript_bac_3 = $transcript_bac_3;
            $professional_qualification->equivalence_certificate = $equivalence_certificate;
            $professional_qualification->provisional_image = $provisional_image;
            $professional_qualification->character_image = $character_image;
            $professional_qualification->intership_image = $intership_image;
            $professional_qualification->noc_image = $noc_image;
            $professional_qualification->visa_image = $visa_image;
            $professional_qualification->passport_image = $passport_image;
            $professional_qualification->save();
            $response['data'] = null;
            $response['message'] = 'Specialization updated successfully';
            $response['error'] = null;
            $response['status'] = 200;
            return response()->json($response, $response['status']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

}