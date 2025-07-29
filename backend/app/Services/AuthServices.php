<?php

namespace App\Services;

use Illuminate\Support\Facades\Hash;
use App\Models\User;
class AuthServices
{
        public function register(array $data)
    {
        $data['password'] = Hash::make($data['password']);
        $user = User::create([
            'name' => $data['name'],
            'email'      => $data['email'],
            'password'   => $data['password'],
        ]);
        if(!$user){
            throw new \Exception('User registration failed');
        }
        
       $token = auth('api')->login($user); 

        return [
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ];

    }

    public function login(array $data)
    {
        if (! $token = auth('api')->attempt($data)) {
            throw new \Exception('Invalid credentials');
        }

        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'user' => auth('api')->user()
        ];
    }

    public function logout()
    {
        auth()->logout();
    }

    public function refresh()
    {
        $token = auth()->refresh();
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ];
    }

    public function profile()
    {
        return auth()->user();
    }

}