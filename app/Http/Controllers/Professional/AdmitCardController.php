<?php

namespace App\Http\Controllers\Professional;

use App\Http\Controllers\Controller;
use App\Models\Applicant;
use Illuminate\Support\Facades\Auth;

class AdmitCardController extends Controller
{
    public function admitcard()
    {
        $data['nav'] = 'admitcard';
        $data['sub_nav'] = '';
        $data['page_title'] = "admitcard";
        $data['applicant'] = Applicant::where('professional_id', auth('professional')->id())->first();
        return view('professional.admitcard.index', $data);
    }
}
