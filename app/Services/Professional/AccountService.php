<?php
namespace App\Services\Professional;

use App\Models\Professional;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AccountService
{
    public function store($request)
    {
        try {
            $id = Auth::guard('professional')->id();
            $professional = Professional::findOrFail($id);
            $professional->name = $request['name'];
            $professional->phone = $request['phone'];
            $professional->save();
            $response['data'] = $professional;
            $response['message'] = 'Profile updated successfully';
            $response['error'] = null;
            $response['status'] = 200;;
            return response()->json($response,  $response['status']);
        } catch (\Exception$e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function updatePassword($request)
    {
        try {
            if (Hash::check($request['old_password'], Auth::guard('professional')->user()->password)) {
                Professional::whereId(Auth::guard('professional')->id())->update([
                    'password' => Hash::make($request['new_password']),
                    'password_reference' => $request['new_password'],
                ]);
                $response['message'] = 'Changed password successfully';
                $response['error'] = null;
                $response['status'] = 200;
                return response()->json($response,  $response['status']);
            } else {
                $response['message'] = null;
                $response['error'] = 'Old password does not match.';
                $response['status'] = 422;
                return response()->json($response,  $response['status']);
            }
        } catch (\Exception$e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

}
