<?php

namespace App\Service;

use App\Models\EmailVerificationToken;
use App\Models\User;
use App\Notifications\EmailVerificationNoti;
use http\Env\Response;
use Illuminate\Support\Str;

class EmailVerificationService
{
    //Generate verification link
    public function generateVerificationLink(string $email)
    {
        $check = EmailVerificationToken::query()->where('email', $email)->first();
        if ($check) $check->delete();
        $token = Str::uuid();
        $url = config('app.url') . '?token=' . $token . '&email=' . $email;
        $saveToken = EmailVerificationToken::query()->create([
            'email' => $email,
            'token' => $token,
            'expired_at' => now()->addMinutes(60),
        ]);
        if ($saveToken) {
            return $url;
        };
    }
    //Send a verification link
    public function sendVerificationLink(object $user): void
    {
        \Notification::send($user, new EmailVerificationNoti($this->generateVerificationLink($user->email)));
    }
    public function checkIfEmailVerified($user):void
    {
        if($user->email_verified_at){
            response()->json([
                'status' => 'failed',
                'message' => 'Email already verified'
            ])->send();
            exit();
        }
    }
    public function verifyEmail(string $email, string $token)
    {
        $user = User::query()->where('email', $email)->first();
        if(!$user){
            response()->json([
                'status' => 'failed',
                'message' => 'User not found'
            ])->send();
            exit();
        }
        $this->checkIfEmailVerified($user);
        $verifiedToken = $this->verifyToken($email, $token);
        if($user->markEmailAsVerified()){
            return response()->json([
                'status' => 'success',
                'message' => 'Email verified successfully'
            ]);
        }else{
            return response()->json([
                'status' => 'failed',
                'message' => 'Email verification failed'
            ],500);
        }
    }
    public function verifyToken(string $email, string $token)
    {
        $token = EmailVerificationToken::query()->where('email', $email)->where('token', $token)->first();
        if($token){
            if($token->expired_at >= now()){
                return $token;
            }else{
                $token->delete();
                response()->json([
                    'status' => 'failed',
                    'message' => 'Token expired'
                ])->send();
                exit();
            }
        }else{
            response()->json([
                'status' => 'failed',
                'message' => 'Invalid token'
            ], 401)->send();
            exit();
        }
    }
}
