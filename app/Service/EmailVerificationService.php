<?php

namespace App\Service;

use App\Models\EmailVerificationToken;
use App\Notifications\EmailVerificationNoti;
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
}
