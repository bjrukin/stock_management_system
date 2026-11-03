<?php
namespace App\Services\Site;

use App\Jobs\Site\OtpPasswordJob;
use App\Models\Professional;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthService {
    public function checkLogin($request) {
        try {
            $check_data = ['email' => $request['email'], 'password' => $request['password']];
            $guard = Professional::where('email', '=', $request['email'])->first();
            if (! $guard) {
                $response['error'] = 'Email Not Matched!';
                $response['status'] = 406;
                return response()->json($response, $response['status']);
            }
            if (Auth::guard('professional')->attempt($check_data)) {
                $response['data'] = null;
                $response['message'] = 'Login sucessfully.';
                $response['error'] = null;
                $response['status'] = 200;
                return response()->json($response, $response['status']);
            } else {
                $response['error'] = 'Email or Password Not Matched!';
                $response['status'] = 406;
                return response()->json($response, $response['status']);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function otp($request) {
        try {
            DB::table('password_reset_tokens')->where('email', $request['email'])->delete();
            $otp = passwordResetOtp();
            DB::table('password_reset_tokens')->insert([
                'email'      => $request['email'],
                'token'      => $otp,
                'created_at' => Carbon::now(),
            ]);

            $professional = Professional::where('email', $request['email'])->first();
            $email_data = [
                'otp'     => $otp,
                'name'    => $professional->name,
                'email'   => $professional->email,
                'subject' => 'Reset Password',
            ];

            $jobToDispatch = (new OtpPasswordJob($email_data))->delay(Carbon::now()->addSeconds(1));
            dispatch($jobToDispatch);
            $response['message'] = 'We have mailed OTP to  reset password.';
            $response['error'] = null;
            $response['status'] = 200;
            return response()->json($response, $response['status']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function savePassword($request) {
        try {
            $updatePassword = DB::table('password_reset_tokens')
                ->where('token', $request['otp'])
                ->first();
            if ($updatePassword) {
                Professional::where('email', $updatePassword->email)->update([
                    'password'           => Hash::make($request['new_password']),
                    'password_reference' => $request['new_password'],
                ]);
                DB::table('password_reset_tokens')->where('email', $updatePassword->email)->delete();
                $response['message'] = 'Passsword changed sucessfully.';
                $response['error'] = null;
                $response['status'] = 200;
                return response()->json($response, $response['status']);
            } else {
                $response['message'] = 'OTP entered is incorrect';
                $response['status'] = 422;
                $response['error'] = true;
                return response()->json($response, $response['status']);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function registerSave($request) {
        try {
            $professional = new Professional;
            $professional->name = $request['name'];
            $professional->phone = $request['phone'];
            $professional->email = $request['email'];
            $professional->is_foreign = $request['is_foreign'];
            $professional->password = Hash::make($request['password']);
            $professional->password_reference = $request['password'];
            $professional->save();
            $response['data'] = null;
            $response['message'] = 'Registered successfully';
            $response['error'] = null;
            $response['status'] = 201;
            return response()->json($response, $response['status']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
