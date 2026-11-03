<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Level\StoreRequest;
use App\Models\Level;
use App\Services\Admin\LevelService;
use Illuminate\Http\Request;

class LevelController extends Controller
{

    public function __construct(protected LevelService $service)
    {
    }

    public function list(Request $request)
    {
        $data['nav'] = 'academics';
        $data['sub_nav'] = 'level';
        $data['page_title'] = 'Level';
        if ($request->ajax()) {
            return $this->service->list();
        }
        return view('admin.level.list', $data);
    }

    public function order(Request $request)
    {
        return $this->service->order($request);
    }
    public function status(Request $request)
    {
        return $this->service->status($request);
    }
    public function addEdit(Request $request)
    {
        $id = ($request->id) ? $request->id : 0;
        $data['nav'] = 'academics';
        $data['sub_nav'] = 'level';
        $data['page_title'] =  ($id == 0) ? "Add Level" : "Edit Level";
        $data['action'] = route('admin.level.store');
        $data['row'] = Level::where('id', $id)->first();
        return view('admin.level.form', $data);
    }

    public function store(StoreRequest $request)
    {
        return $this->service->store($request->validated());
    }

    public function delete(Request $request)
    {
        return $this->service->delete($request);
    }
}
