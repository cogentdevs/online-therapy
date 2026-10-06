<?php

namespace App\Http\Controllers\Api\Account;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Account\ResendTwoFactorChallengeRequest;
use App\Http\Requests\Api\Account\StartTwoFactorChallengeRequest;
use App\Http\Requests\Api\Account\VerifyTwoFactorChallengeRequest;
use App\Services\Api\AccountTwoFactorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SecurityController extends Controller
{
    public function __construct(private readonly AccountTwoFactorService $twoFactorService) {}

    public function show(Request $request): JsonResponse
    {
        return response()->json(['success' => true, 'data' => ['two_factor' => $this->twoFactorService->state($request->user())]]);
    }

    public function storeChallenge(StartTwoFactorChallengeRequest $request): JsonResponse
    {
        return $this->challengeResponse($this->twoFactorService->start($request->user(), $request->validated('action')));
    }

    public function verifyChallenge(VerifyTwoFactorChallengeRequest $request): JsonResponse
    {
        $result = $this->twoFactorService->verify($request->user(), $request->validated('challenge_id'), $request->validated('otp'));
        if ($result['status'] === 'verified') {
            return response()->json(['success' => true, 'message' => 'Two-factor authentication updated successfully.', 'data' => ['two_factor' => $result['state']]]);
        }

        $message = match ($result['status']) {
            'attempts' => 'The maximum verification attempts have been reached.',
            'expired' => 'The verification challenge has expired.',
            'state' => 'The two-factor authentication state has changed.',
            'invalid_challenge' => 'The verification challenge is invalid.',
            default => 'The verification code is invalid.',
        };

        return response()->json(['success' => false, 'message' => $message], 422);
    }

    public function resendChallenge(ResendTwoFactorChallengeRequest $request): JsonResponse
    {
        return $this->challengeResponse($this->twoFactorService->resend($request->user(), $request->validated('challenge_id')));
    }

    /** @param array<string, mixed> $result */
    private function challengeResponse(array $result): JsonResponse
    {
        if (in_array($result['status'], ['issued', 'resent'], true)) {
            return response()->json(['success' => true, 'message' => 'A verification code was sent to your email.', 'data' => $result['challenge']]);
        }
        if ($result['status'] === 'throttled') {
            return response()->json(['success' => false, 'message' => 'Please wait before requesting another code.', 'data' => ['retry_after' => $result['retry_after']]], 429);
        }

        return response()->json(['success' => false, 'message' => $result['status'] === 'state'
            ? 'The requested two-factor action is not available in the current state.'
            : 'The verification challenge is invalid.'], 422);
    }
}
