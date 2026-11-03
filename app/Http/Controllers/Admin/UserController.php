<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\User\StoreRequest;
use App\Models\Admin as User;
use App\Models\Role;
use App\Services\Admin\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{

    public function __construct(protected UserService $service)
    {
    }

    public function list(Request $request)
    {        
        $data['nav'] = 'user';
        $data['sub_nav'] = 'user';
        $data['page_title'] = 'System Users';
        if ($request->ajax()) {
            return $this->service->list();
        }
        return view('admin.user.list', $data);
    }
    public function status(Request $request)
    {
        return $this->service->status($request);
    }

    public function addEdit(Request $request)
    {
        $id = ($request->id) ? $request->id : 0;
        $data['nav'] = 'user';
        $data['sub_nav'] = 'user';
        $data['page_title'] =  ($id == 0) ? "Add System User" : "Edit System User";
        $data['action'] = route('admin.user.store');
        $data['row'] = User::where('id', $id)->first();
        $data['roles'] = Role::where('id', '<>', 1)->orderBy('name', 'asc')->get();
        return view('admin.user.form', $data);
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
