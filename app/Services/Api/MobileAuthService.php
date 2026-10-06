<?php

namespace App\Services\Api;

use App\Mail\AccountActivationLinkMailToUser;
use App\Mail\ForgotPasswordLinkMailToUser;
use App\Mail\NewUserRegisterMailToAdmin;
use App\Mail\TwoFactorOtpMailToUser;
use App\Mail\WelcomeMailToUser;
use App\Models\User;
use App\Models\UserTwoFactorChallenge;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class MobileAuthService
{
    /** @param array{name: string, email: string, phone: string, password: string} $data */
    public function register(array $data): User
    {
        return DB::transaction(function () use ($data): User {
            $token = Str::random(64);
            $user = new User([
                'name' => $data['name'], 'email' => Str::lower($data['email']), 'phone' => $data['phone'],
                'profile_image' => 'user-avatar.png', 'password' => $data['password'], 'is_active' => false,
            ]);
            $user->activation_token = hash('sha256', $token);
            $user->activation_token_expires_at = now()->addDay();
            $user->save();
            $user->assignRole(Role::findByName('user', 'web'));
            $user->twoFactorSetting()->create(['method' => null, 'is_enabled' => false, 'verified_at' => null]);

            Mail::to('cogentdevs@gmail.com')->send(new NewUserRegisterMailToAdmin($user));
            Mail::to($user)->send(new AccountActivationLinkMailToUser(
                $user,
                route('front.account.activation', ['token' => $token]),
            ));

            return $user;
        });
    }

    public function inspectActivation(string $token): string
    {
        return $this->activationState($this->activationUser($token), $token);
    }

    public function activate(string $token): string
    {
        return DB::transaction(function () use ($token): string {
            $user = User::query()->where('activation_token', hash('sha256', $token))
                ->orWhere('consumed_activation_token_hash', hash('sha256', $token))->lockForUpdate()->first();
            $state = $this->activationState($user, $token);

            if ($state !== 'pending') {
                return $state;
            }

            $user->forceFill([
                'is_active' => true, 'consumed_activation_token_hash' => $user->activation_token,
                'activation_token' => null, 'activation_token_expires_at' => null,
            ])->save();
            Mail::to($user)->send(new WelcomeMailToUser($user));

            return 'activated';
        });
    }

    /** @return array{challenge_id: string, masked_destination: string} */
    public function issueLoginChallenge(User $user): array
    {
        $previousHash = $user->twoFactorChallenges()->where('purpose', 'login')->whereNull('consumed_at')
            ->latest('id')->value('otp_hash');
        do {
            $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        } while (is_string($previousHash) && Hash::check($otp, $previousHash));

        $challenge = DB::transaction(function () use ($user, $otp): UserTwoFactorChallenge {
            $user->twoFactorChallenges()->where('purpose', 'login')->whereNull('consumed_at')->update(['consumed_at' => now()]);

            return $user->twoFactorChallenges()->create([
                'purpose' => 'login', 'channel' => 'email', 'otp_hash' => Hash::make($otp),
                'expires_at' => now()->addMinutes(10), 'attempts' => 0,
            ]);
        });
        RateLimiter::hit($this->twoFactorRateKey($user), 60);
        Mail::to($user)->send(new TwoFactorOtpMailToUser($user, $otp, 'login'));

        return ['challenge_id' => $this->challengeToken($challenge), 'masked_destination' => $this->maskEmail($user->email)];
    }

    /** @return array{status: string, user: User|null} */
    public function verifyLoginChallenge(string $challengeToken, string $otp): array
    {
        $challenge = $this->resolveChallenge($challengeToken);
        if ($challenge === null) {
            return ['status' => 'invalid_challenge', 'user' => null];
        }

        return DB::transaction(function () use ($challenge, $otp): array {
            $challenge = UserTwoFactorChallenge::query()->lockForUpdate()->find($challenge->id);
            if ($challenge === null || $challenge->consumed_at !== null || $challenge->expires_at->isPast()) {
                return ['status' => 'expired', 'user' => null];
            }
            if ($challenge->attempts >= 5) {
                return ['status' => 'attempts', 'user' => null];
            }
            if (! Hash::check($otp, $challenge->otp_hash)) {
                $challenge->increment('attempts');

                return ['status' => 'invalid', 'user' => null];
            }

            $user = User::query()->with('twoFactorSetting')->find($challenge->user_id);
            if (! $this->eligibleTwoFactorUser($user)) {
                $challenge->update(['consumed_at' => now()]);

                return ['status' => 'state', 'user' => null];
            }
            $challenge->update(['consumed_at' => now()]);

            return ['status' => 'verified', 'user' => $user];
        });
    }

    /** @return array{status: string, challenge?: array{challenge_id: string, masked_destination: string}} */
    public function resendLoginChallenge(string $challengeToken): array
    {
        $challenge = $this->resolveChallenge($challengeToken);
        $user = $challenge?->user;
        if ($challenge === null || ! $this->eligibleTwoFactorUser($user)) {
            return ['status' => 'invalid_challenge'];
        }
        if (RateLimiter::tooManyAttempts($this->twoFactorRateKey($user), 1)) {
            return ['status' => 'throttled'];
        }

        return ['status' => 'resent', 'challenge' => $this->issueLoginChallenge($user)];
    }

    public function sendPasswordResetLink(string $email): void
    {
        $user = User::query()->whereRaw('LOWER(email) = ?', [Str::lower($email)])->first();
        if ($user?->hasRole('user', 'web') !== true) {
            return;
        }
        $token = Password::broker('users')->createToken($user);
        Mail::to($user)->send(new ForgotPasswordLinkMailToUser(
            $user,
            route('front.password.reset', ['token' => $token, 'email' => $user->email]),
            (int) config('auth.passwords.users.expire', 60),
        ));
    }

    /** @param array{email: string, password: string, password_confirmation: string, token: string} $credentials */
    public function resetPassword(array $credentials): bool
    {
        $user = User::query()->where('email', $credentials['email'])->first();
        if ($user?->hasRole('user', 'web') !== true) {
            return false;
        }

        return Password::broker('users')->reset($credentials, function (User $user, string $password): void {
            $user->forceFill(['password' => Hash::make($password), 'remember_token' => Str::random(60)])->save();
            event(new PasswordReset($user));
        }) === Password::PasswordReset;
    }

    private function activationUser(string $token): ?User
    {
        $hash = hash('sha256', $token);

        return User::query()->where('activation_token', $hash)->orWhere('consumed_activation_token_hash', $hash)->first();
    }

    private function activationState(?User $user, string $token): string
    {
        if ($user === null || ! $user->hasRole('user', 'web')) {
            return 'invalid';
        }
        $hash = hash('sha256', $token);
        $pending = is_string($user->activation_token) && hash_equals($user->activation_token, $hash);
        $consumed = is_string($user->consumed_activation_token_hash) && hash_equals($user->consumed_activation_token_hash, $hash);
        if ($user->is_active && ($pending || $consumed)) {
            return 'active';
        }
        if (! $pending) {
            return 'invalid';
        }

        return $user->activation_token_expires_at?->isFuture() === true ? 'pending' : 'expired';
    }

    private function challengeToken(UserTwoFactorChallenge $challenge): string
    {
        return Crypt::encryptString(json_encode(['id' => $challenge->id, 'user_id' => $challenge->user_id], JSON_THROW_ON_ERROR));
    }

    private function resolveChallenge(string $token): ?UserTwoFactorChallenge
    {
        try {
            $payload = json_decode(Crypt::decryptString($token), true, flags: JSON_THROW_ON_ERROR);
        } catch (DecryptException|\JsonException) {
            return null;
        }
        if (! is_array($payload) || ! isset($payload['id'], $payload['user_id'])) {
            return null;
        }

        return UserTwoFactorChallenge::query()->with(['user.twoFactorSetting'])
            ->whereKey($payload['id'])->where('user_id', $payload['user_id'])
            ->where('purpose', 'login')->where('channel', 'email')->first();
    }

    private function eligibleTwoFactorUser(?User $user): bool
    {
        return $user !== null && $user->is_active && $user->hasRole('user', 'web')
            && $user->twoFactorSetting?->is_enabled === true && $user->twoFactorSetting->method === 'email';
    }

    private function twoFactorRateKey(User $user): string
    {
        return 'api-2fa-login:'.$user->id;
    }

    private function maskEmail(string $email): string
    {
        [$local, $domain] = array_pad(explode('@', $email, 2), 2, '');
        $visible = mb_substr($local, 0, min(2, mb_strlen($local)));

        return $visible.str_repeat('*', max(3, mb_strlen($local) - mb_strlen($visible))).'@'.$domain;
    }
}
