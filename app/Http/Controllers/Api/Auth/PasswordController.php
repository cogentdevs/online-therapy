<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\ForgotPasswordRequest;
use App\Http\Requests\Api\Auth\ResetPasswordRequest;
use App\Services\Api\MobileAuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class PasswordController extends Controller
{
    public function __construct(private readonly MobileAuthService $mobileAuthService) {}

    public function forgot(ForgotPasswordRequest $request): JsonResponse
    {
        $request->ensureIsNotRateLimited();
        $request->hitRateLimiter();
        $this->mobileAuthService->sendPasswordResetLink($request->validated('email'));

        return response()->json(['success' => true, 'message' => 'If the email belongs to an eligible account, a password reset link has been sent.']);
    }

    public function reset(ResetPasswordRequest $request): JsonResponse
    {
        if (! $this->mobileAuthService->resetPassword($request->safe()->only(['token', 'email', 'password', 'password_confirmation']))) {
            throw ValidationException::withMessages(['email' => 'The password reset token is invalid or has expired.']);
        }

        return response()->json(['success' => true, 'message' => 'Password reset successfully.']);
    }
}
