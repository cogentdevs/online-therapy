<?php

namespace App\Services\Api;

use App\Mail\TwoFactorOtpMailToUser;
use App\Models\User;
use App\Models\UserTwoFactorChallenge;
use App\Models\UserTwoFactorSetting;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;

class AccountTwoFactorService
{
    /** @return array<string, mixed> */
    public function state(User $user): array
    {
        $setting = $user->twoFactorSetting;
        $enabled = $setting?->is_enabled === true;

        return [
            'enabled' => $enabled,
            'method' => $enabled ? $setting->method : null,
            'available_methods' => ['email'],
            'masked_destination' => $this->maskEmail($user->email),
        ];
    }

    /** @return array{status: string, challenge?: array<string, string>, retry_after?: int} */
    public function start(User $user, string $action): array
    {
        if (! $this->validState($user, $action)) {
            return ['status' => 'state'];
        }
        $key = $this->rateKey($user, $action);
        if (RateLimiter::tooManyAttempts($key, 1)) {
            return ['status' => 'throttled', 'retry_after' => RateLimiter::availableIn($key)];
        }

        return ['status' => 'issued', 'challenge' => $this->issue($user, $action)];
    }

    /** @return array{status: string, state?: array<string, mixed>} */
    public function verify(User $user, string $token, string $otp): array
    {
        $challenge = $this->resolve($user, $token);
        if ($challenge === null) {
            return ['status' => 'invalid_challenge'];
        }

        return DB::transaction(function () use ($user, $challenge, $otp): array {
            $challenge = UserTwoFactorChallenge::query()->whereKey($challenge->id)->lockForUpdate()->first();
            if ($challenge === null || $challenge->consumed_at !== null || $challenge->expires_at->isPast()) {
                return ['status' => 'expired'];
            }
            if ($challenge->attempts >= 5) {
                return ['status' => 'attempts'];
            }
            if (! Hash::check($otp, $challenge->otp_hash)) {
                $challenge->increment('attempts');

                return ['status' => 'invalid'];
            }
            if (! $this->validState($user->fresh('twoFactorSetting'), $challenge->purpose)) {
                $challenge->update(['consumed_at' => now()]);

                return ['status' => 'state'];
            }

            $setting = UserTwoFactorSetting::query()->firstOrCreate(
                ['user_id' => $user->getKey()],
                ['method' => null, 'is_enabled' => false, 'verified_at' => null],
            );
            $setting->update($challenge->purpose === 'enable'
                ? ['method' => 'email', 'is_enabled' => true, 'verified_at' => now()]
                : ['method' => null, 'is_enabled' => false, 'verified_at' => null]);
            $challenge->update(['consumed_at' => now()]);

            return ['status' => 'verified', 'state' => $this->state($user->fresh('twoFactorSetting'))];
        });
    }

    /** @return array{status: string, challenge?: array<string, string>, retry_after?: int} */
    public function resend(User $user, string $token): array
    {
        $challenge = $this->resolve($user, $token);
        if ($challenge === null || $challenge->consumed_at !== null || $challenge->expires_at->isPast()
            || $challenge->attempts >= 5 || ! $this->validState($user, $challenge->purpose)) {
            return ['status' => 'invalid_challenge'];
        }
        $key = $this->rateKey($user, $challenge->purpose);
        if (RateLimiter::tooManyAttempts($key, 1)) {
            return ['status' => 'throttled', 'retry_after' => RateLimiter::availableIn($key)];
        }

        return ['status' => 'resent', 'challenge' => $this->issue($user, $challenge->purpose)];
    }

    /** @return array{challenge_id: string, action: string, delivery_method: string, masked_destination: string} */
    private function issue(User $user, string $purpose): array
    {
        $previousHash = $user->twoFactorChallenges()->where('purpose', $purpose)->whereNull('consumed_at')->latest('id')->value('otp_hash');
        do {
            $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        } while (is_string($previousHash) && Hash::check($otp, $previousHash));

        $challenge = DB::transaction(function () use ($user, $purpose, $otp): UserTwoFactorChallenge {
            $user->twoFactorSetting()->firstOrCreate([], ['method' => null, 'is_enabled' => false, 'verified_at' => null]);
            $user->twoFactorChallenges()->where('purpose', $purpose)->whereNull('consumed_at')->update(['consumed_at' => now()]);

            return $user->twoFactorChallenges()->create([
                'purpose' => $purpose, 'channel' => 'email', 'otp_hash' => Hash::make($otp),
                'expires_at' => now()->addMinutes(10), 'attempts' => 0,
            ]);
        });
        RateLimiter::hit($this->rateKey($user, $purpose), 60);
        Mail::to($user)->send(new TwoFactorOtpMailToUser($user, $otp, $purpose));

        return [
            'challenge_id' => Crypt::encryptString(json_encode([
                'id' => $challenge->id, 'user_id' => $user->id, 'purpose' => $purpose,
            ], JSON_THROW_ON_ERROR)),
            'action' => $purpose, 'delivery_method' => 'email', 'masked_destination' => $this->maskEmail($user->email),
        ];
    }

    private function resolve(User $user, string $token): ?UserTwoFactorChallenge
    {
        try {
            $payload = json_decode(Crypt::decryptString($token), true, flags: JSON_THROW_ON_ERROR);
        } catch (DecryptException|\JsonException) {
            return null;
        }
        if (! is_array($payload) || ! isset($payload['id'], $payload['user_id'], $payload['purpose'])
            || (int) $payload['user_id'] !== (int) $user->getKey()
            || ! in_array($payload['purpose'], ['enable', 'disable'], true)) {
            return null;
        }

        return UserTwoFactorChallenge::query()->whereKey($payload['id'])->where('user_id', $user->getKey())
            ->where('purpose', $payload['purpose'])->where('channel', 'email')->first();
    }

    private function validState(User $user, string $action): bool
    {
        $setting = $user->twoFactorSetting;

        return $action === 'enable'
            ? $setting?->is_enabled !== true
            : $action === 'disable' && $setting?->is_enabled === true && $setting->method === 'email';
    }

    private function rateKey(User $user, string $purpose): string
    {
        return 'api-2fa-account-'.$purpose.':'.$user->getKey();
    }

    private function maskEmail(string $email): string
    {
        [$local, $domain] = array_pad(explode('@', $email, 2), 2, '');
        $visible = mb_substr($local, 0, min(2, mb_strlen($local)));

        return $visible.str_repeat('*', max(3, mb_strlen($local) - mb_strlen($visible))).'@'.$domain;
    }
}
