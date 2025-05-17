<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(LoginRequest $request){
        try{
            if(!$token = Auth::attempt($request->validated())){
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid credentials'
                ]);
            }
            $user = Auth::user();
            return $this->responseWithToken($token, $user);
        }catch (\Exception $e){
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ],500);
        }
    }
    public function register(RegisterRequest $request){
        try {
            $user = User::query()->create($request->validated());
            $token = Auth::login($user);
            return $this->responseWithToken($token, $user);
        }catch (\Exception $e){
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    public function responseWithToken($token, $user){
        return response()->json([
            'status' => 'success',
            'user' => $user,
            'token' => $token,
            'type' => 'Bearer',
        ]);
    }
}
