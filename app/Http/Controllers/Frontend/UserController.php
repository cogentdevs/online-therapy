<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\ChangePasswordRequest;
use App\Http\Requests\Frontend\ChangeTwoFactorMethodRequest;
use App\Http\Requests\Frontend\DisableTwoFactorRequest;
use App\Http\Requests\Frontend\ForgotPasswordRequest;
use App\Http\Requests\Frontend\LoginUserRequest;
use App\Http\Requests\Frontend\RegisterUserRequest;
use App\Http\Requests\Frontend\ResetPasswordRequest;
use App\Http\Requests\Frontend\SendTwoFactorOtpRequest;
use App\Http\Requests\Frontend\UpdateProfileRequest;
use App\Http\Requests\Frontend\VerifyTwoFactorOtpRequest;
use App\Mail\AccountActivationLinkMailToUser;
use App\Mail\ForgotPasswordLinkMailToUser;
use App\Mail\NewUserRegisterMailToAdmin;
use App\Mail\TwoFactorOtpMailToUser;
use App\Mail\WelcomeMailToUser;
use App\Models\SubscriptionProduct;
use App\Models\SubscriptionType;
use App\Models\User;
use App\Models\UserSubscription;
use App\Models\UserTwoFactorChallenge;
use App\Models\UserTwoFactorSetting;
use App\Models\Video;
use App\Services\BookmarkPresentationService;
use App\Services\FrontendAccountDataTableService;
use App\Services\PaidContentLoginIntentService;
use App\Services\SubscriptionEntitlementService;
use App\Services\SubscriptionPresentationService;
use App\Services\UserContentVisitPresentationService;
use Closure;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    private const PENDING_LOGIN_SESSION_KEY = 'front_two_factor_login';

    public function __construct(
        private readonly BookmarkPresentationService $bookmarkPresentationService,
        private readonly FrontendAccountDataTableService $frontendAccountDataTableService,
        private readonly SubscriptionEntitlementService $subscriptionEntitlementService,
        private readonly SubscriptionPresentationService $subscriptionPresentationService,
        private readonly PaidContentLoginIntentService $paidContentLoginIntentService,
        private readonly UserContentVisitPresentationService $userContentVisitPresentationService,
    ) {}

    public function register(): View
    {
        return view('frontend.user-register');
    }

    public function login(): View
    {
        return view('frontend.user-login');
    }

    public function forgotPassword(): View
    {
        return view('frontend.user-forgot-password');
    }

    public function emailPasswordResetLink(ForgotPasswordRequest $request): RedirectResponse
    {
        $request->ensureIsNotRateLimited();
        $request->hitRateLimiter();

        $email = Str::lower($request->validated('email'));
        $user = User::query()->whereRaw('LOWER(email) = ?', [$email])->first();

        if ($user?->hasRole('user', 'web')) {
            $broker = Password::broker('users');
            $token = $broker->createToken($user);
            $resetUrl = route('front.password.reset', ['token' => $token, 'email' => $user->email]);
            $expiresInMinutes = (int) config('auth.passwords.users.expire', 60);

            Mail::to($user)->send(new ForgotPasswordLinkMailToUser($user, $resetUrl, $expiresInMinutes));
        }

        return to_route('front.password.request')->with(
            'success',
            'اگر یہ ای میل کسی اہل اکاؤنٹ سے منسلک ہے تو پاس ورڈ ری سیٹ لنک بھیج دیا گیا ہے۔'
        );
    }

    public function resetPassword(Request $request, string $token): Response
    {
        $email = $request->query('email');
        $user = is_string($email) ? User::query()->where('email', $email)->first() : null;
        $isValid = $user?->hasRole('user', 'web') === true
            && Password::broker('users')->tokenExists($user, $token);

        return response()->view('frontend.user-reset-password', [
            'email' => is_string($email) ? $email : '',
            'token' => $token,
            'isValid' => $isValid,
        ])->header('Cache-Control', 'no-store, private')
            ->header('Referrer-Policy', 'no-referrer');
    }

    public function updatePassword(ResetPasswordRequest $request): RedirectResponse
    {
        $credentials = $request->safe()->only(['email', 'password', 'password_confirmation', 'token']);
        $user = User::query()->where('email', $credentials['email'])->first();

        if ($user?->hasRole('user', 'web') !== true) {
            return back()->withErrors(['email' => 'یہ پاس ورڈ ری سیٹ لنک درست نہیں ہے یا ایکسپائر ہو چکا ہے۔']);
        }

        $status = Password::broker('users')->reset(
            $credentials,
            function (User $user, string $password): void {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status !== Password::PasswordReset) {
            return back()->withErrors(['email' => 'یہ پاس ورڈ ری سیٹ لنک درست نہیں ہے یا ایکسپائر ہو چکا ہے۔']);
        }

        return to_route('front.login')->with(
            'success',
            'آپ کا پاس ورڈ کامیابی سے تبدیل ہو گیا ہے۔ اب آپ اپنے نئے پاس ورڈ کے ساتھ لاگ ان کر سکتے ہیں۔'
        );
    }

    public function account(Request $request): View
    {
        $user = $request->user('web');
        $activeSubscriptions = $this->subscriptionEntitlementService->activeSubscriptions($user);
        $this->subscriptionPresentationService->prepare($activeSubscriptions);
        $activeEntitlementTypes = $activeSubscriptions
            ->flatMap(fn (UserSubscription $subscription) => $subscription->subscriptionTypes)
            ->unique(fn (SubscriptionType $type): int => $type->getKey())
            ->values();
        $activeSubscriptionPreview = $activeSubscriptions->take(3);
        $remainingActiveSubscriptionCount = max(0, $activeSubscriptions->count() - $activeSubscriptionPreview->count());
        $recentBookmarks = $user->bookmarks()
            ->with('bookmarkable')
            ->latest('created_at')
            ->latest('id')
            ->limit(5)
            ->get();
        $this->bookmarkPresentationService->prepare($recentBookmarks);
        $recentActivities = $user->contentVisits()
            ->with('visitable')
            ->orderByDesc('last_visited_at')
            ->orderByDesc('id')
            ->limit(5)
            ->get();
        $this->userContentVisitPresentationService->prepare($recentActivities);

        return view('frontend.user-account.index', compact(
            'activeEntitlementTypes',
            'activeSubscriptionPreview',
            'activeSubscriptions',
            'remainingActiveSubscriptionCount',
            'recentBookmarks',
            'recentActivities',
            'user',
        ));
    }

    public function profile(Request $request): View
    {
        return view('frontend.user-account.profile', ['user' => $request->user('web')]);
    }

    public function subscriptions(Request $request): View|JsonResponse
    {
        if ($request->boolean('datatable')) {
            return response()->json($this->frontendAccountDataTableService->subscriptions($request->user('web'), $request));
        }

        $subscriptions = $request->user('web')->userSubscriptions()
            ->with(['subscriptionTypes', 'currency'])
            ->latest('created_at')
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        $this->subscriptionPresentationService->prepare($subscriptions->getCollection());

        return view('frontend.user-account.subscriptions', compact('subscriptions'));
    }

    public function planVideos(Request $request, int $subscription): View
    {
        $userSubscription = $this->videoAccessiblePlanSubscription($request, $subscription);
        $userSubscription->load([
            'subscriptionProduct.videos' => fn ($query) => $query
                ->where('videos.is_active', true)
                ->where('videos.status', Video::STATUS_PUBLISHED)
                ->orderBy('videos.title')
                ->select(['videos.id', 'videos.thumbnail', 'videos.title', 'videos.short_description']),
        ]);

        return view('frontend.user-account.plan-videos', compact('userSubscription'));
    }

    public function watchPlanVideo(Request $request, int $subscription, int $video): RedirectResponse
    {
        $userSubscription = $this->videoAccessiblePlanSubscription($request, $subscription);
        $planVideo = $userSubscription->subscriptionProduct
            ->videos()
            ->where('videos.is_active', true)
            ->where('videos.status', Video::STATUS_PUBLISHED)
            ->findOrFail($video, ['videos.id', 'videos.video_link']);

        return redirect()->away($planVideo->video_link);
    }

    private function videoAccessiblePlanSubscription(Request $request, int $subscriptionId): UserSubscription
    {
        return $request->user('web')->userSubscriptions()
            ->currentlyActive()
            ->where('product_for', SubscriptionProduct::FOR_PLAN)
            ->with('subscriptionProduct')
            ->findOrFail($subscriptionId);
    }

    public function bookmarks(Request $request): View|JsonResponse
    {
        if ($request->boolean('datatable')) {
            return response()->json($this->frontendAccountDataTableService->bookmarks($request->user('web'), $request));
        }

        $bookmarks = $request->user('web')->bookmarks()
            ->with('bookmarkable')
            ->latest('created_at')
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        $this->bookmarkPresentationService->prepare($bookmarks->getCollection());

        return view('frontend.user-account.bookmarks', compact('bookmarks'));
    }

    public function recentActivities(Request $request): View|JsonResponse
    {
        if ($request->boolean('datatable')) {
            return response()->json($this->frontendAccountDataTableService->recentActivities($request->user('web'), $request));
        }

        $activities = $request->user('web')->contentVisits()
            ->with('visitable')
            ->orderByDesc('last_visited_at')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        $this->userContentVisitPresentationService->prepare($activities->getCollection());

        return view('frontend.user-account.recent-activities', compact('activities'));
    }

    public function updateProfile(UpdateProfileRequest $request): RedirectResponse
    {
        $user = $request->user('web');
        $oldFilename = is_string($user->profile_image) ? $user->profile_image : 'user-avatar.png';
        $newFilename = null;

        if ($request->hasFile('profile_image')) {
            $profileImage = $request->file('profile_image');
            $newFilename = 'user-'.$user->getKey().'-'.Str::random(20).'.'.$profileImage->extension();
            File::ensureDirectoryExists(public_path('images/frontend-images/users'));
            $profileImage->move(public_path('images/frontend-images/users'), $newFilename);
        }

        try {
            DB::transaction(function () use ($request, $user, $newFilename): void {
                $user->name = $request->validated('name');
                $user->email = $request->validated('email');
                $user->phone = $request->validated('phone');

                if ($newFilename !== null) {
                    $user->profile_image = $newFilename;
                } elseif ($request->boolean('remove_profile_image') || ! is_string($user->profile_image) || $user->profile_image === '') {
                    $user->profile_image = 'user-avatar.png';
                }

                $user->save();
            });
        } catch (\Throwable $exception) {
            if ($newFilename !== null) {
                $this->deleteFrontendProfileImage($newFilename);
            }

            throw $exception;
        }

        if ($newFilename !== null || $request->boolean('remove_profile_image')) {
            $this->deleteFrontendProfileImage($oldFilename);
        }

        return to_route('front.account.profile')->with('success', 'آپ کا پروفائل کامیابی سے اپ ڈیٹ ہو گیا ہے۔');
    }

    public function changePassword(): View
    {
        return view('frontend.user-account.change-password');
    }

    public function twoFactorAuthentication(Request $request): View
    {
        $setting = $request->user('web')->twoFactorSetting()->firstOrCreate([], [
            'method' => null,
            'is_enabled' => false,
            'verified_at' => null,
        ]);

        return view('frontend.user-account.two-factor-authentication', ['setting' => $setting]);
    }

    public function sendTwoFactorOtp(SendTwoFactorOtpRequest $request): RedirectResponse
    {
        if ($request->validated('method') !== 'email') {
            return back()->withErrors(['method' => 'فون کے ذریعے تصدیق فی الحال دستیاب نہیں ہے۔']);
        }

        $user = $request->user('web');
        $rateLimitKey = 'front-2fa-enable:'.$user->getKey();
        if (RateLimiter::tooManyAttempts($rateLimitKey, 1)) {
            return back()->withErrors([
                'method' => 'نیا کوڈ حاصل کرنے کے لیے '.RateLimiter::availableIn($rateLimitKey).' سیکنڈ انتظار کریں۔',
            ]);
        }

        $otp = $this->generateTwoFactorOtp($user, 'enable');
        DB::transaction(function () use ($user, $otp): void {
            $user->twoFactorSetting()->firstOrCreate([], [
                'method' => null,
                'is_enabled' => false,
                'verified_at' => null,
            ]);
            $user->twoFactorChallenges()
                ->where('purpose', 'enable')
                ->whereNull('consumed_at')
                ->update(['consumed_at' => now()]);
            $user->twoFactorChallenges()->create([
                'purpose' => 'enable',
                'channel' => 'email',
                'otp_hash' => Hash::make($otp),
                'expires_at' => now()->addMinutes(10),
                'attempts' => 0,
            ]);
        });

        RateLimiter::hit($rateLimitKey, 60);
        Mail::to($user)->send(new TwoFactorOtpMailToUser($user, $otp));

        return to_route('front.account.two-factor-authentication.verify')
            ->with('success', 'تصدیقی کوڈ آپ کی ای میل پر بھیج دیا گیا ہے۔');
    }

    public function verifyTwoFactorAuthentication(Request $request): RedirectResponse|View
    {
        $challenge = $this->activeEnableTwoFactorChallenge($request);
        if ($challenge === null) {
            return to_route('front.account.two-factor-authentication')
                ->withErrors(['otp' => 'کوئی فعال تصدیقی درخواست موجود نہیں ہے۔']);
        }

        return view('frontend.user-account.two-factor-verify', [
            'challenge' => $challenge,
            'purpose' => 'enable',
            'verifyRoute' => route('front.account.two-factor-authentication.verify.store'),
            'resendRoute' => route('front.account.two-factor-authentication.send'),
        ]);
    }

    public function storeTwoFactorVerification(VerifyTwoFactorOtpRequest $request): RedirectResponse
    {
        $result = DB::transaction(function () use ($request): string {
            $challenge = UserTwoFactorChallenge::query()
                ->where('user_id', $request->user('web')->getKey())
                ->where('purpose', 'enable')
                ->where('channel', 'email')
                ->whereNull('consumed_at')
                ->latest('id')
                ->lockForUpdate()
                ->first();

            if ($challenge === null || $challenge->expires_at->isPast()) {
                return 'expired';
            }
            if ($challenge->attempts >= 5) {
                return 'attempts';
            }
            if (! Hash::check($request->validated('otp'), $challenge->otp_hash)) {
                $challenge->increment('attempts');

                return 'invalid';
            }

            $challenge->update(['consumed_at' => now()]);
            UserTwoFactorSetting::query()->firstOrCreate(
                ['user_id' => $request->user('web')->getKey()],
                ['method' => null, 'is_enabled' => false, 'verified_at' => null]
            )->update(['method' => 'email', 'is_enabled' => true, 'verified_at' => now()]);

            return 'enabled';
        });

        if ($result === 'enabled') {
            return to_route('front.account.two-factor-authentication')
                ->with('success', 'دو مرحلہ توثیق کامیابی سے فعال ہو گئی ہے۔');
        }

        $message = match ($result) {
            'attempts' => 'تصدیقی کوششوں کی حد مکمل ہو چکی ہے۔ نیا کوڈ حاصل کریں۔',
            'expired' => 'تصدیقی کوڈ ایکسپائر ہو چکا ہے۔ نیا کوڈ حاصل کریں۔',
            default => 'تصدیقی کوڈ درست نہیں ہے۔',
        };

        return back()->withErrors(['otp' => $message]);
    }

    public function sendDisableTwoFactorOtp(DisableTwoFactorRequest $request): RedirectResponse
    {
        $user = $request->user('web');
        $setting = $user->twoFactorSetting;
        if (! $setting?->is_enabled) {
            return back()->withErrors(['current_password' => 'دو مرحلہ توثیق پہلے ہی غیر فعال ہے۔']);
        }
        if ($setting->method !== 'email') {
            return back()->withErrors(['current_password' => 'موجودہ تصدیقی طریقہ فی الحال دستیاب نہیں ہے۔']);
        }
        if ($response = $this->accountTwoFactorCooldownResponse($user, 'disable')) {
            return $response;
        }

        $this->issueAccountTwoFactorChallenge($user, 'disable', 'email');

        return to_route('front.account.two-factor-authentication.disable.challenge')
            ->with('success', 'تصدیقی کوڈ آپ کی ای میل پر بھیج دیا گیا ہے۔');
    }

    public function disableTwoFactorVerification(Request $request): RedirectResponse|View
    {
        return $this->accountTwoFactorVerificationPage($request, 'disable', 'email');
    }

    public function verifyDisableTwoFactorOtp(VerifyTwoFactorOtpRequest $request): RedirectResponse
    {
        $user = $request->user('web');
        $result = $this->consumeAccountTwoFactorChallenge($user, 'disable', 'email', $request->validated('otp'), function () use ($user): bool {
            $setting = UserTwoFactorSetting::query()->where('user_id', $user->getKey())->lockForUpdate()->first();
            if (! $setting?->is_enabled || $setting->method !== 'email') {
                return false;
            }
            $setting->update(['is_enabled' => false, 'method' => null, 'verified_at' => null]);

            return true;
        });

        return $this->accountTwoFactorVerificationResponse($result, 'disable');
    }

    public function resendDisableTwoFactorOtp(Request $request): RedirectResponse
    {
        $user = $request->user('web');
        $setting = $user->twoFactorSetting;
        if (! $setting?->is_enabled || $setting->method !== 'email' || ! $this->hasActiveAccountChallenge($user, 'disable', 'email')) {
            return to_route('front.account.two-factor-authentication')->withErrors(['otp' => 'تصدیقی درخواست درست نہیں ہے۔']);
        }
        if ($response = $this->accountTwoFactorCooldownResponse($user, 'disable')) {
            return $response;
        }
        $this->issueAccountTwoFactorChallenge($user, 'disable', 'email');

        return back()->with('success', 'نیا تصدیقی کوڈ آپ کی ای میل پر بھیج دیا گیا ہے۔');
    }

    public function sendChangeTwoFactorMethodOtp(ChangeTwoFactorMethodRequest $request): RedirectResponse
    {
        $user = $request->user('web');
        $setting = $user->twoFactorSetting;
        $method = $request->validated('method');
        if (! $setting?->is_enabled) {
            return back()->withErrors(['method' => 'دو مرحلہ توثیق فعال نہیں ہے۔']);
        }
        if ($method === $setting->method) {
            return back()->withErrors(['method' => 'منتخب طریقہ پہلے ہی فعال ہے۔']);
        }
        if ($method !== 'email') {
            return back()->withErrors(['method' => 'فون کے ذریعے تصدیق فی الحال دستیاب نہیں ہے۔']);
        }
        if ($response = $this->accountTwoFactorCooldownResponse($user, 'change_method')) {
            return $response;
        }
        $this->issueAccountTwoFactorChallenge($user, 'change_method', $method);

        return to_route('front.account.two-factor-authentication.change-method.challenge')
            ->with('success', 'نئے طریقے کی تصدیق کے لیے کوڈ بھیج دیا گیا ہے۔');
    }

    public function changeTwoFactorMethodVerification(Request $request): RedirectResponse|View
    {
        return $this->accountTwoFactorVerificationPage($request, 'change_method', 'email');
    }

    public function verifyChangeTwoFactorMethodOtp(VerifyTwoFactorOtpRequest $request): RedirectResponse
    {
        $user = $request->user('web');
        $result = $this->consumeAccountTwoFactorChallenge($user, 'change_method', 'email', $request->validated('otp'), function () use ($user): bool {
            $setting = UserTwoFactorSetting::query()->where('user_id', $user->getKey())->lockForUpdate()->first();
            if (! $setting?->is_enabled || $setting->method === 'email') {
                return false;
            }
            $setting->update(['method' => 'email', 'verified_at' => now()]);

            return true;
        });

        return $this->accountTwoFactorVerificationResponse($result, 'change_method');
    }

    public function resendChangeTwoFactorMethodOtp(Request $request): RedirectResponse
    {
        $user = $request->user('web');
        $setting = $user->twoFactorSetting;
        if (! $setting?->is_enabled || $setting->method === 'email' || ! $this->hasActiveAccountChallenge($user, 'change_method', 'email')) {
            return to_route('front.account.two-factor-authentication')->withErrors(['otp' => 'تصدیقی درخواست درست نہیں ہے۔']);
        }
        if ($response = $this->accountTwoFactorCooldownResponse($user, 'change_method')) {
            return $response;
        }
        $this->issueAccountTwoFactorChallenge($user, 'change_method', 'email');

        return back()->with('success', 'نیا تصدیقی کوڈ آپ کی ای میل پر بھیج دیا گیا ہے۔');
    }

    public function updateAccountPassword(ChangePasswordRequest $request): RedirectResponse
    {
        $user = $request->user('web');

        DB::transaction(function () use ($request, $user): void {
            $user->forceFill([
                'password' => Hash::make($request->validated('password')),
                'remember_token' => Str::random(60),
            ])->save();
        });

        return to_route('front.account.change-password')
            ->with('success', 'آپ کا پاس ورڈ کامیابی سے تبدیل ہو گیا ہے۔');
    }

    public function authenticate(LoginUserRequest $request): JsonResponse|RedirectResponse
    {
        $user = $request->authenticate();
        $intended = $this->frontendIntendedUrl($request);
        $setting = $user->twoFactorSetting;

        if ($setting?->is_enabled) {
            if ($setting->method !== 'email') {
                $message = 'اس اکاؤنٹ کا تصدیقی طریقہ فی الحال دستیاب نہیں ہے۔';

                return $request->expectsJson()
                    ? response()->json(['message' => $message, 'errors' => ['email' => [$message]]], 422)
                    : back()->withErrors(['email' => $message]);
            }

            $this->issueLoginTwoFactorChallenge($user);
            $request->session()->regenerate();
            $request->session()->put(self::PENDING_LOGIN_SESSION_KEY, [
                'user_id' => $user->getKey(),
                'remember' => $request->boolean('remember'),
                'intended' => $intended,
                'method' => 'email',
            ]);
            $request->session()->forget('url.intended');

            return $request->expectsJson()
                ? response()->json(['redirect' => route('front.two-factor.challenge'), 'two_factor_required' => true])
                : to_route('front.two-factor.challenge');
        }

        Auth::guard('web')->login($user, $request->boolean('remember'));
        $request->session()->regenerate();
        $request->session()->forget('url.intended');

        return $request->expectsJson()
            ? response()->json(['redirect' => $intended])
            : redirect()->to($intended);
    }

    public function loginTwoFactorChallenge(Request $request): RedirectResponse|View
    {
        $user = $this->pendingLoginUser($request);
        if ($user === null) {
            return $this->cancelPendingLogin($request);
        }

        $challengeExists = $user->twoFactorChallenges()
            ->where('purpose', 'login')->where('channel', 'email')->whereNull('consumed_at')->exists();
        if (! $challengeExists) {
            return $this->cancelPendingLogin($request);
        }

        return view('frontend.user-two-factor-challenge', [
            'maskedEmail' => $this->maskEmail($user->email),
        ]);
    }

    public function verifyLoginTwoFactorChallenge(VerifyTwoFactorOtpRequest $request): RedirectResponse
    {
        $user = $this->pendingLoginUser($request);
        if ($user === null) {
            return $this->cancelPendingLogin($request);
        }

        $result = DB::transaction(function () use ($request, $user): string {
            $challenge = $user->twoFactorChallenges()
                ->where('purpose', 'login')
                ->where('channel', 'email')
                ->whereNull('consumed_at')
                ->latest('id')
                ->lockForUpdate()
                ->first();

            if ($challenge === null || $challenge->expires_at->isPast()) {
                return 'expired';
            }
            if ($challenge->attempts >= 5) {
                return 'attempts';
            }
            if (! Hash::check($request->validated('otp'), $challenge->otp_hash)) {
                $challenge->increment('attempts');

                return 'invalid';
            }

            $challenge->update(['consumed_at' => now()]);

            return 'verified';
        });

        if ($result !== 'verified') {
            if ($result !== 'invalid') {
                return $this->cancelPendingLogin($request, $result === 'expired'
                    ? 'تصدیقی کوڈ ایکسپائر یا استعمال ہو چکا ہے۔ دوبارہ لاگ ان کریں۔'
                    : 'تصدیقی کوششوں کی حد مکمل ہو چکی ہے۔ دوبارہ لاگ ان کریں۔');
            }

            return back()->withErrors(['otp' => 'تصدیقی کوڈ درست نہیں ہے۔']);
        }

        $pending = $request->session()->get(self::PENDING_LOGIN_SESSION_KEY);
        Auth::guard('web')->login($user, (bool) ($pending['remember'] ?? false));
        $intended = is_string($pending['intended'] ?? null) ? $pending['intended'] : route('front.account');
        $request->session()->regenerate();
        $request->session()->forget(self::PENDING_LOGIN_SESSION_KEY);

        return redirect()->to($intended);
    }

    public function resendLoginTwoFactorChallenge(Request $request): RedirectResponse
    {
        $user = $this->pendingLoginUser($request);
        if ($user === null) {
            return $this->cancelPendingLogin($request);
        }

        $rateLimitKey = 'front-2fa-login:'.$user->getKey();
        if (RateLimiter::tooManyAttempts($rateLimitKey, 1)) {
            return back()->withErrors([
                'otp' => 'نیا کوڈ حاصل کرنے کے لیے '.RateLimiter::availableIn($rateLimitKey).' سیکنڈ انتظار کریں۔',
            ]);
        }

        $this->issueLoginTwoFactorChallenge($user);

        return back()->with('success', 'نیا تصدیقی کوڈ آپ کی ای میل پر بھیج دیا گیا ہے۔');
    }

    public function logout(Request $request): RedirectResponse
    {
        abort_unless($request->user('web')?->hasRole('user', 'web'), 403);
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return to_route('frontend.home');
    }

    public function store(RegisterUserRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $role = Role::findByName('user', 'web');
        $adminEmail = 'cogentdevs@gmail.com';

        DB::transaction(function () use ($data, $role, $adminEmail): void {
            $token = Str::random(64);
            $user = new User([
                'name' => $data['first_name'].' '.$data['last_name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'profile_image' => 'user-avatar.png',
                'password' => $data['password'],
                'is_active' => false,
            ]);
            $user->activation_token = hash('sha256', $token);
            $user->activation_token_expires_at = now()->addDay();
            $user->save();
            $user->assignRole($role);
            $user->twoFactorSetting()->create([
                'method' => null,
                'is_enabled' => false,
                'verified_at' => null,
            ]);

            $activationUrl = route('front.account.activation', ['token' => $token]);
            Mail::to($adminEmail)->send(new NewUserRegisterMailToAdmin($user));
            Mail::to($user)->send(new AccountActivationLinkMailToUser($user, $activationUrl));
        });

        return to_route('front.register')->with('success', 'آپ کی رجسٹریشن مکمل ہو گئی ہے۔ اکاؤنٹ فعال کرنے کے لیے اپنی ای میل چیک کریں۔');
    }

    public function activation(Request $request): Response
    {
        $token = $this->activationToken($request);
        $user = $token === null ? null : User::query()
            ->where('activation_token', hash('sha256', $token))
            ->orWhere('consumed_activation_token_hash', hash('sha256', $token))
            ->first();

        return $this->activationPage($this->activationState($user, $token), $token);
    }

    public function activate(Request $request): Response|RedirectResponse
    {
        $token = $this->activationToken($request);
        if ($token === null) {
            return $this->activationPage('invalid');
        }

        $state = DB::transaction(function () use ($token): string {
            $hash = hash('sha256', $token);
            $user = User::query()
                ->where('activation_token', $hash)
                ->orWhere('consumed_activation_token_hash', $hash)
                ->lockForUpdate()
                ->first();
            $state = $this->activationState($user, $token);

            if ($state !== 'pending') {
                return $state;
            }

            $user->is_active = true;
            $user->consumed_activation_token_hash = $user->activation_token;
            $user->activation_token = null;
            $user->activation_token_expires_at = null;
            $user->save();

            Mail::to($user)->send(new WelcomeMailToUser($user));

            return 'activated';
        });

        if ($state !== 'activated') {
            return $this->activationPage($state);
        }

        return to_route('front.login')
            ->with('success', 'آپ کا اکاؤنٹ کامیابی سے ایکٹیویٹ ہو گیا ہے۔ اب آپ اپنے ای میل اور پاس ورڈ کے ذریعے لاگ ان کر سکتے ہیں۔')
            ->header('Referrer-Policy', 'no-referrer');
    }

    private function activationToken(Request $request): ?string
    {
        $token = $request->query('token');

        return is_string($token) && preg_match('/\A[a-zA-Z0-9]{64}\z/', $token) === 1 ? $token : null;
    }

    private function activeEnableTwoFactorChallenge(Request $request): ?UserTwoFactorChallenge
    {
        return $request->user('web')->twoFactorChallenges()
            ->where('purpose', 'enable')
            ->where('channel', 'email')
            ->whereNull('consumed_at')
            ->latest('id')
            ->first();
    }

    private function generateTwoFactorOtp(User $user, string $purpose): string
    {
        $previousHash = $user->twoFactorChallenges()
            ->where('purpose', $purpose)
            ->whereNull('consumed_at')
            ->latest('id')
            ->value('otp_hash');

        do {
            $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        } while (is_string($previousHash) && Hash::check($otp, $previousHash));

        return $otp;
    }

    private function issueLoginTwoFactorChallenge(User $user): void
    {
        $otp = $this->generateTwoFactorOtp($user, 'login');

        DB::transaction(function () use ($user, $otp): void {
            $user->twoFactorChallenges()
                ->where('purpose', 'login')
                ->whereNull('consumed_at')
                ->update(['consumed_at' => now()]);
            $user->twoFactorChallenges()->create([
                'purpose' => 'login',
                'channel' => 'email',
                'otp_hash' => Hash::make($otp),
                'expires_at' => now()->addMinutes(10),
                'attempts' => 0,
            ]);
        });

        RateLimiter::hit('front-2fa-login:'.$user->getKey(), 60);
        Mail::to($user)->send(new TwoFactorOtpMailToUser($user, $otp, 'login'));
    }

    private function issueAccountTwoFactorChallenge(User $user, string $purpose, string $channel): void
    {
        $otp = $this->generateTwoFactorOtp($user, $purpose);

        DB::transaction(function () use ($user, $purpose, $channel, $otp): void {
            $user->twoFactorChallenges()->where('purpose', $purpose)->whereNull('consumed_at')
                ->update(['consumed_at' => now()]);
            $user->twoFactorChallenges()->create([
                'purpose' => $purpose,
                'channel' => $channel,
                'otp_hash' => Hash::make($otp),
                'expires_at' => now()->addMinutes(10),
                'attempts' => 0,
            ]);
        });

        RateLimiter::hit('front-2fa-'.$purpose.':'.$user->getKey(), 60);
        Mail::to($user)->send(new TwoFactorOtpMailToUser($user, $otp, $purpose));
    }

    private function consumeAccountTwoFactorChallenge(User $user, string $purpose, string $channel, string $otp, Closure $onVerified): string
    {
        return DB::transaction(function () use ($user, $purpose, $channel, $otp, $onVerified): string {
            $challenge = $user->twoFactorChallenges()
                ->where('purpose', $purpose)->where('channel', $channel)->whereNull('consumed_at')
                ->latest('id')->lockForUpdate()->first();

            if ($challenge === null || $challenge->expires_at->isPast()) {
                return 'expired';
            }
            if ($challenge->attempts >= 5) {
                return 'attempts';
            }
            if (! Hash::check($otp, $challenge->otp_hash)) {
                $challenge->increment('attempts');

                return 'invalid';
            }
            if (! $onVerified()) {
                $challenge->update(['consumed_at' => now()]);

                return 'state';
            }

            $challenge->update(['consumed_at' => now()]);

            return 'verified';
        });
    }

    private function accountTwoFactorVerificationPage(Request $request, string $purpose, string $channel): RedirectResponse|View
    {
        $challenge = $request->user('web')->twoFactorChallenges()
            ->where('purpose', $purpose)->where('channel', $channel)->whereNull('consumed_at')
            ->latest('id')->first();
        if ($challenge === null) {
            return to_route('front.account.two-factor-authentication')->withErrors(['otp' => 'کوئی فعال تصدیقی درخواست موجود نہیں ہے۔']);
        }

        $routePrefix = 'front.account.two-factor-authentication.'.str_replace('_', '-', $purpose);

        return view('frontend.user-account.two-factor-verify', [
            'challenge' => $challenge,
            'purpose' => $purpose,
            'verifyRoute' => route($routePrefix.'.verify'),
            'resendRoute' => route($routePrefix.'.resend'),
        ]);
    }

    private function accountTwoFactorVerificationResponse(string $result, string $purpose): RedirectResponse
    {
        if ($result === 'verified') {
            $message = $purpose === 'disable'
                ? 'دو مرحلہ توثیق کامیابی سے غیر فعال کر دی گئی ہے۔'
                : 'دو مرحلہ توثیق کا طریقہ کامیابی سے تبدیل ہو گیا ہے۔';

            return to_route('front.account.two-factor-authentication')->with('success', $message);
        }

        $message = match ($result) {
            'attempts' => 'تصدیقی کوششوں کی حد مکمل ہو چکی ہے۔ نیا کوڈ حاصل کریں۔',
            'expired' => 'تصدیقی کوڈ ایکسپائر یا استعمال ہو چکا ہے۔',
            'state' => 'دو مرحلہ توثیق کی موجودہ حالت تبدیل ہو چکی ہے۔',
            default => 'تصدیقی کوڈ درست نہیں ہے۔',
        };

        return back()->withErrors(['otp' => $message]);
    }

    private function accountTwoFactorCooldownResponse(User $user, string $purpose): ?RedirectResponse
    {
        $key = 'front-2fa-'.$purpose.':'.$user->getKey();
        if (! RateLimiter::tooManyAttempts($key, 1)) {
            return null;
        }

        return back()->withErrors(['otp' => 'نیا کوڈ حاصل کرنے کے لیے '.RateLimiter::availableIn($key).' سیکنڈ انتظار کریں۔']);
    }

    private function hasActiveAccountChallenge(User $user, string $purpose, string $channel): bool
    {
        return $user->twoFactorChallenges()
            ->where('purpose', $purpose)->where('channel', $channel)->whereNull('consumed_at')->exists();
    }

    private function pendingLoginUser(Request $request): ?User
    {
        $pending = $request->session()->get(self::PENDING_LOGIN_SESSION_KEY);
        if (! is_array($pending) || ! is_numeric($pending['user_id'] ?? null) || ($pending['method'] ?? null) !== 'email') {
            return null;
        }

        $user = User::query()->find((int) $pending['user_id']);
        $setting = $user?->twoFactorSetting;

        return $user?->hasRole('user', 'web') === true
            && $user->is_active
            && $setting?->is_enabled
            && $setting->method === 'email'
                ? $user
                : null;
    }

    private function cancelPendingLogin(Request $request, string $message = 'آپ کا تصدیقی سیشن درست نہیں ہے۔ دوبارہ لاگ ان کریں۔'): RedirectResponse
    {
        $request->session()->forget(self::PENDING_LOGIN_SESSION_KEY);

        return to_route('front.login')->withErrors(['email' => $message]);
    }

    private function frontendIntendedUrl(Request $request): string
    {
        $subscriptionProductId = $request->session()->pull('front_subscription_product_id');
        if (is_numeric($subscriptionProductId)) {
            $subscriptionProduct = SubscriptionProduct::query()
                ->availableToFrontend()
                ->find((int) $subscriptionProductId);

            if ($subscriptionProduct !== null) {
                return route('front.subscriptions.checkout', $subscriptionProduct);
            }
        }

        $paidContentDestination = $this->paidContentLoginIntentService->pullValidDestination($request);
        if ($paidContentDestination !== null) {
            return $paidContentDestination;
        }

        if ($request->string('login_source')->toString() === 'homepage') {
            return route('frontend.home');
        }

        $intended = $request->session()->get('url.intended');
        if (! is_string($intended)
            || ! Str::startsWith($intended, rtrim(url('/'), '/').'/')
            || Str::startsWith($intended, route('admin.login'))
            || Str::startsWith($intended, [
                route('front.login'),
                route('front.register'),
                route('front.account.activation'),
                route('front.logout'),
                route('front.two-factor.challenge'),
            ])) {
            return route('front.account');
        }

        return $intended;
    }

    private function maskEmail(string $email): string
    {
        [$local, $domain] = array_pad(explode('@', $email, 2), 2, '');
        $visible = mb_substr($local, 0, min(2, mb_strlen($local)));

        return $visible.str_repeat('*', max(3, mb_strlen($local) - mb_strlen($visible))).'@'.$domain;
    }

    private function deleteFrontendProfileImage(string $filename): void
    {
        if ($filename === 'user-avatar.png' || basename($filename) !== $filename) {
            return;
        }

        File::delete(public_path('images/frontend-images/users/'.$filename));
    }

    private function activationState(?User $user, ?string $token): string
    {
        if ($user === null || $token === null || ! $user->hasRole('user', 'web')) {
            return 'invalid';
        }

        $hash = hash('sha256', $token);
        $matchesPendingToken = is_string($user->activation_token) && hash_equals($user->activation_token, $hash);
        $matchesConsumedToken = is_string($user->consumed_activation_token_hash)
            && hash_equals($user->consumed_activation_token_hash, $hash);

        if ($user->is_active && ($matchesPendingToken || $matchesConsumedToken)) {
            return 'active';
        }

        if (! $matchesPendingToken) {
            return 'invalid';
        }

        if ($user->activation_token_expires_at === null || $user->activation_token_expires_at->lessThanOrEqualTo(now())) {
            return 'expired';
        }

        return 'pending';
    }

    private function activationPage(string $state, ?string $token = null): Response
    {
        return response()->view('frontend.user-activate-account', [
            'state' => $state,
            'activationAction' => $state === 'pending' ? route('front.account.activation.store', ['token' => $token]) : null,
        ])->header('Cache-Control', 'no-store, private')
            ->header('Referrer-Policy', 'no-referrer');
    }
}
