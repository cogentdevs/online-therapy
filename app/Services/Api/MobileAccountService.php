<?php

namespace App\Services\Api;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MobileAccountService
{
    /** @return array<string, mixed> */
    public function userData(User $user): array
    {
        $user->loadMissing('twoFactorSetting');

        return [
            'id' => $user->id, 'name' => $user->name, 'email' => $user->email, 'phone' => $user->phone,
            'profile_image_url' => $user->frontendProfileImageUrl(),
            'two_factor_enabled' => $user->twoFactorSetting?->is_enabled === true,
            'two_factor_method' => $user->twoFactorSetting?->is_enabled === true ? $user->twoFactorSetting->method : null,
        ];
    }

    /** @param array{name: string, email: string, phone: string} $data */
    public function updateProfile(User $user, array $data, ?UploadedFile $profileImage): User
    {
        $oldFilename = is_string($user->profile_image) ? $user->profile_image : 'user-avatar.png';
        $newFilename = null;
        if ($profileImage !== null) {
            $newFilename = 'user-'.$user->getKey().'-'.Str::random(20).'.'.$profileImage->extension();
            File::ensureDirectoryExists(public_path('images/frontend-images/users'));
            $profileImage->move(public_path('images/frontend-images/users'), $newFilename);
        }

        try {
            DB::transaction(function () use ($user, $data, $newFilename): void {
                $user->name = $data['name'];
                $user->email = $data['email'];
                $user->phone = $data['phone'];
                if ($newFilename !== null) {
                    $user->profile_image = $newFilename;
                }
                $user->save();
            });
        } catch (\Throwable $exception) {
            if ($newFilename !== null) {
                $this->deleteProfileImage($newFilename);
            }
            throw $exception;
        }

        if ($newFilename !== null) {
            $this->deleteProfileImage($oldFilename);
        }

        return $user->fresh(['twoFactorSetting']);
    }

    public function removeProfileImage(User $user): User
    {
        $oldFilename = is_string($user->profile_image) ? $user->profile_image : 'user-avatar.png';
        DB::transaction(fn () => $user->forceFill(['profile_image' => 'user-avatar.png'])->save());
        $this->deleteProfileImage($oldFilename);

        return $user->fresh(['twoFactorSetting']);
    }

    public function changePassword(User $user, string $password): void
    {
        DB::transaction(fn () => $user->forceFill([
            'password' => Hash::make($password), 'remember_token' => Str::random(60),
        ])->save());
    }

    private function deleteProfileImage(string $filename): void
    {
        if ($filename === 'user-avatar.png' || basename($filename) !== $filename) {
            return;
        }
        File::delete(public_path('images/frontend-images/users/'.$filename));
    }
}
