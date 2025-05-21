<?php

namespace App\Service;

use App\Exceptions\InvalidPasswordReset;
use Illuminate\Http\Exceptions\HttpResponseException;

class PasswordService
{
    /**
     * @throws InvalidPasswordReset
     */
    public function validateCurrentPassword(string $password): void
    {
        if (!password_verify($password, auth()->user()->password)) {
            throw new InvalidPasswordReset("Current password is incorrect");
        }
    }
}
