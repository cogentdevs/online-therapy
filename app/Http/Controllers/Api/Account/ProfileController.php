<?php

namespace App\Http\Controllers\Api\Account;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Account\ChangePasswordRequest;
use App\Http\Requests\Api\Account\UpdateProfileRequest;
use App\Services\Api\MobileAccountService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function __construct(private readonly MobileAccountService $mobileAccountService) {}

    public function show(Request $request): JsonResponse
    {
        return response()->json(['success' => true, 'data' => ['user' => $this->mobileAccountService->userData($request->user())]]);
    }

    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $user = $this->mobileAccountService->updateProfile(
            $request->user(), $request->safe()->only(['name', 'email', 'phone']), $request->file('profile_image'),
        );

        return response()->json(['success' => true, 'message' => 'Profile updated successfully.', 'data' => ['user' => $this->mobileAccountService->userData($user)]]);
    }

    public function destroyProfileImage(Request $request): JsonResponse
    {
        $user = $this->mobileAccountService->removeProfileImage($request->user());

        return response()->json(['success' => true, 'message' => 'Profile image removed successfully.', 'data' => ['user' => $this->mobileAccountService->userData($user)]]);
    }

    public function updatePassword(ChangePasswordRequest $request): JsonResponse
    {
        $this->mobileAccountService->changePassword($request->user(), $request->validated('password'));

        return response()->json(['success' => true, 'message' => 'Password changed successfully.']);
    }
}
