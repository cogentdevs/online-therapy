<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\TwoFactorChallengeRequest;
use App\Http\Requests\Api\Auth\TwoFactorVerifyRequest;
use App\Services\Api\MobileAuthService;
use Illuminate\Http\JsonResponse;

class TwoFactorController extends Controller
{
    public function __construct(private readonly MobileAuthService $mobileAuthService) {}

    public function verify(TwoFactorVerifyRequest $request, AuthController $authController): JsonResponse
    {
        $result = $this->mobileAuthService->verifyLoginChallenge($request->validated('challenge_id'), $request->validated('otp'));
        if ($result['status'] !== 'verified') {
            $message = match ($result['status']) {
                'expired' => 'The two-factor challenge has expired or was already used.',
                'attempts' => 'The maximum verification attempts have been reached.',
                'state' => 'The account security state has changed. Log in again.',
                'invalid_challenge' => 'The two-factor challenge is invalid.',
                default => 'The verification code is invalid.',
            };

            return response()->json(['success' => false, 'message' => $message], 422);
        }

        return response()->json(['success' => true, 'data' => $authController->authenticatedData($result['user'])]);
    }

    public function resend(TwoFactorChallengeRequest $request): JsonResponse
    {
        $result = $this->mobileAuthService->resendLoginChallenge($request->validated('challenge_id'));
        if ($result['status'] === 'throttled') {
            return response()->json(['success' => false, 'message' => 'Please wait before requesting another code.'], 429);
        }
        if ($result['status'] !== 'resent') {
            return response()->json(['success' => false, 'message' => 'The two-factor challenge is invalid.'], 422);
        }

        return response()->json(['success' => true, 'message' => 'A new verification code has been sent.', 'data' => $result['challenge']]);
    }
}
