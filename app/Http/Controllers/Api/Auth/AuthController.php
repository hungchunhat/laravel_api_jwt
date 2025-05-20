<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\ResendEmailVerificationRequest;
use App\Http\Requests\VerifyEmailRequest;
use App\Models\User;
use App\Service\EmailVerificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    //Dependency Injection
    public EmailVerificationService $emailVerificationService;

    public function __construct(EmailVerificationService $emailVerificationService)
    {
        $this->emailVerificationService = $emailVerificationService;
    }

    public function login(LoginRequest $request)
    {
        try {
            if (!$token = Auth::attempt($request->validated())) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid credentials'
                ]);
            }
            $user = Auth::user();
            return $this->responseWithToken($token, $user);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function register(RegisterRequest $request)
    {
        try {
            $user = User::query()->create($request->validated());
            $this->emailVerificationService->sendVerificationLink($user);
            $token = Auth::login($user);
            return $this->responseWithToken($token, $user);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function responseWithToken($token, $user)
    {
        return response()->json([
            'status' => 'success',
            'user' => $user,
            'token' => $token,
            'type' => 'Bearer',
        ]);
    }

    public function verifyUserEmail(VerifyEmailRequest $request)
    {
        return $this->emailVerificationService->verifyEmail($request->get('email'), $request->get('token'));
    }

    public function resendVerificationLink(ResendEmailVerificationRequest $request)
    {
        try {
            $user = User::query()->where('email', $request->get('email'))->first();
            if ($user) {
                $this->emailVerificationService->sendVerificationLink($user);
                return response()->json([
                    'status' => 'success',
                    'message' => 'Verification link sent successfully'
                ]);
            }
            else{
                return response()->json([
                    'status' => 'failed',
                    'message' => 'User not found'
                ]);
            }
        }catch (\Exception $e){
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
}
