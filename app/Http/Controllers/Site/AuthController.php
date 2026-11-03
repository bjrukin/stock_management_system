<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Http\Requests\Site\Auth\AuthRequest;
use App\Http\Requests\Site\Auth\OtpRequest;
use App\Http\Requests\Site\Auth\RegisterRequest;
use App\Http\Requests\Site\Auth\SavePasswordRequest;
use App\Services\Site\AuthService;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller {
    public function __construct(protected AuthService $service) {
      
    }

    public function login() {
        if (Auth::guard('professional')->id()) {
            return redirect()->route('professional.dashboard');
        }
        $data['page_title'] = 'Login';
        return view('site.auth.login', $data);
    }

    public function checkLogin(AuthRequest $request) {
        return $this->service->checkLogin($request->validated());
    }

    public function forgotPassword() {
        $data['page_title'] = 'Forgot Password';
        return view('site.auth.forgot_password', $data);
    }

    public function otp(OtpRequest $request) {
        return $this->service->otp($request->validated());
    }

    public function resetPassword() {
        $data['page_title'] = 'Reset Password';
        return view('site.auth.reset_password', $data);
    }

    public function savePassword(SavePasswordRequest $request) {
        return $this->service->savePassword($request->validated());
    }

    public function register($type) {

        $data['is_foreign'] = ($type == 'nepal') ? 0 : 1;
        $data['button_value'] = ($type == 'nepal') ? 'Register' : 'Register as Foreign';
        $data['page_title'] = 'Register';
        return view('site.auth.register', $data);
    }

    public function registerSave(RegisterRequest $request) {
        return $this->service->registerSave($request->validated());
    }

}
