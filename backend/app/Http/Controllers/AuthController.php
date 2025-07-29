<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;
use App\Services\AuthServices;
use App\Http\Requests\RegisterRequest;

class AuthController extends Controller
{
    use ApiResponse;

    protected $authServices;

    public function __construct(AuthServices $authServices)
    {
        $this->authServices = $authServices;
    }

    public function register(RegisterRequest $request)
    {
        try {
            $user = $this->authServices->register($request->all());
            return $this->success( 'User registered successfully',$user);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode() ?: 400);
        }
    }

        public function login(LoginRequest $request)
    {
        try {
            $token = $this->authServices->login($request->all());
            return $this->success( 'Login successful',$token);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode() ?: 401);
        }
    }

    public function logout()
    {
        try {
            $this->authServices->logout();
            return $this->success( 'Logout successful');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode() ?: 400);
        }
    }

    public function refresh()
    {
        try {
            $token = $this->authServices->refresh();
            return $this->success( 'Token refreshed successfully',$token);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode() ?: 400);
        }
    }

    public function profile()
    {
        try {
            $user = $this->authServices->profile();
            return $this->success( 'User profile fetched',$user);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode() ?: 400);
        }
    }
}
