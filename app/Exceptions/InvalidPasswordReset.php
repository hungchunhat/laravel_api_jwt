<?php

namespace App\Exceptions;

use Exception;

class InvalidPasswordReset extends Exception
{
    public function __construct(string $message = 'Invalid password reset token', int $code = 401){
        parent::__construct($message, $code);
    }
}
