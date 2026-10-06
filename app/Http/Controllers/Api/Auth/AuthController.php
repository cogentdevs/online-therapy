<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\ActivationRequest;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Models\User;
use App\Services\Api\MobileAuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function __construct(private readonly MobileAuthService $mobileAuthService) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        $user = $this->mobileAuthService->register($request->safe()->only(['name', 'email', 'phone', 'password']));

        return response()->json(['success' => true, 'message' => 'Registration successful. Check your email to activate your account.', 'data' => [
            'requires_activation' => true, 'user' => $this->userData($user),
        ]], 201);
    }

    public function inspectActivation(Request $request): JsonResponse
    {
        $validated = $request->validate(['token' => ['required', 'string', 'size:64', 'regex:/\A[a-zA-Z0-9]+\z/']]);
        $state = $this->mobileAuthService->inspectActivation($validated['token']);

        if (in_array($state, ['invalid', 'expired'], true)) {
            return response()->json(['success' => false, 'message' => $state === 'expired' ? 'Activation token has expired.' : 'Activation token is invalid.'], $state === 'expired' ? 410 : 422);
        }

        return response()->json(['success' => true, 'data' => ['state' => $state, 'can_activate' => $state === 'pending']]);
    }

    public function activate(ActivationRequest $request): JsonResponse
    {
        $state = $this->mobileAuthService->activate($request->validated('token'));

        return match ($state) {
            'activated' => response()->json(['success' => true, 'message' => 'Account activated successfully.']),
            'active' => response()->json(['success' => false, 'message' => 'Account is already active.'], 409),
            'expired' => response()->json(['success' => false, 'message' => 'Activation token has expired.'], 410),
            default => response()->json(['success' => false, 'message' => 'Activation token is invalid.'], 422),
        };
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $user = $request->authenticate();
        $setting = $user->twoFactorSetting;
        if ($setting?->is_enabled) {
            if ($setting->method !== 'email') {
                throw ValidationException::withMessages(['email' => 'The configured two-factor method is unavailable.']);
            }
            $challenge = $this->mobileAuthService->issueLoginChallenge($user);

            return response()->json(['success' => true, 'data' => [
                'requires_2fa' => true, 'challenge_id' => $challenge['challenge_id'],
                'delivery_method' => 'email', 'masked_destination' => $challenge['masked_destination'],
            ]]);
        }

        return response()->json(['success' => true, 'data' => $this->authenticatedData($user)]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json(['success' => true, 'message' => 'Logged out successfully.']);
    }

    /** @return array<string, mixed> */
    public function authenticatedData(User $user): array
    {
        return [
            'requires_2fa' => false, 'token' => $user->createToken('mobile-app')->plainTextToken,
            'token_type' => 'Bearer', 'user' => $this->userData($user),
        ];
    }

    /** @return array<string, mixed> */
    private function userData(User $user): array
    {
        return [
            'id' => $user->id, 'name' => $user->name, 'email' => $user->email, 'phone' => $user->phone,
            'profile_image_url' => $user->frontendProfileImageUrl(),
            'two_factor_enabled' => $user->twoFactorSetting?->is_enabled === true,
        ];
    }
}
