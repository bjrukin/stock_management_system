<?php

namespace App\Http\Controllers\Professional;

use App\Http\Controllers\Controller;
use App\Models\Certificate;


class ICardController extends Controller
{
    public function index()
    {
        $data['nav'] = 'idcard';
        $data['sub_nav'] = '';
        $data['page_title'] = 'idcard';
        $data['certificate'] = Certificate::where('professional_id',  auth('professional')->id())->first();

        return view('professional.icard.index',$data);
    }
}
