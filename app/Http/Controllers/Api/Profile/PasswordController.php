<?php

namespace App\Http\Controllers\Api\Profile;

use App\Exceptions\InvalidPasswordReset;
use App\Http\Controllers\Controller;
use App\Http\Requests\PasswordResetRequest;
use App\Service\PasswordService;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use \Exception;
use Illuminate\Support\Facades\Auth;

class PasswordController extends Controller
{
    private PasswordService $passwordService;
    public function __construct(PasswordService $passwordService)
    {
        $this->passwordService = $passwordService;
    }
    public function resetPassword(PasswordResetRequest $request)
    {
        try{
            $this->passwordService->validateCurrentPassword($request->get('current_password'));
            Auth::user()->password = $request->get('new_password');
            Auth::user()->save();
            return response()->json([
                'status' => 'success',
                'message' => 'Password reset successfully'
            ]);
        }catch (InvalidPasswordReset $e){
            return response()->json([
                'status' => 'failed',
                'message' => $e->getMessage()
            ],$e->getCode());
        }
        catch(Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

}
