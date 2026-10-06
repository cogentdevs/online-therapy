<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdRequest;
use App\Models\Article;
use App\Models\AskQuestion;
use App\Models\Contact;
use App\Models\Magazine;
use App\Models\SubscriptionProduct;
use App\Models\User;
use App\Models\UserSubscription;
use App\Services\ActivityLogService;
use App\Services\AdminContentOwnershipService;
use App\Services\AdminHierarchyService;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AdminController extends Controller
{
    public function __construct(
        private readonly ActivityLogService $activityLogService,
        private readonly AdminContentOwnershipService $adminContentOwnershipService,
        private readonly AdminHierarchyService $adminHierarchyService,
    ) {}

    public function showLogin(Request $request): View|RedirectResponse
    {
        if ($request->user() instanceof User && $request->user()->canAccessAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string'],
            'remember' => ['nullable', 'boolean'],
        ]);

        if (! Auth::attempt([
            'email' => $credentials['email'],
            'password' => $credentials['password'],
        ], $request->boolean('remember'))) {
            return back()
                ->withErrors(['email' => 'The provided credentials are incorrect.'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        $user = Auth::user();

        if (! $user instanceof User || ! $user->isActiveForAdmin()) {
            $this->logoutUser($request);

            return back()
                ->withErrors(['email' => 'Your account is inactive. Please contact an administrator.'])
                ->onlyInput('email');
        }

        if (! $user->canAccessAdmin()) {
            $this->logoutUser($request);

            return back()
                ->withErrors(['email' => 'The provided credentials are incorrect.'])
                ->onlyInput('email');
        }

        return redirect()->route('admin.dashboard');
    }

    public function dashboard(Request $request): View
    {
        $user = $request->user();
        abort_unless($user instanceof User, 403);
        $summaries = [];

        if ($user->can('articles.view')) {
            $summaries['articles'] = $this->contentSummary(
                $this->adminContentOwnershipService->scopeQuery(Article::query(), $user),
                Article::STATUS_PUBLISHED,
            );
        }

        if ($user->can('magazines.view')) {
            $summaries['magazines'] = $this->contentSummary(
                $this->adminContentOwnershipService->scopeQuery(Magazine::query(), $user),
                Magazine::STATUS_PUBLISHED,
            );
        }

        if ($user->can('users.view')) {
            $allowedAdminIds = $this->adminHierarchyService->getAllowedAdminIds($user);
            $summaries['users'] = [
                'total' => $this->adminUsersQuery()
                    ->when($allowedAdminIds !== null, fn (Builder $query) => $query->whereKey($allowedAdminIds))
                    ->count(),
            ];
        }

        if ($user->can('contacts.view')) {
            $summaries['contacts'] = [
                'unread' => Contact::query()->where('is_read', false)->count(),
            ];
        }

        if ($user->can('ask-questions.view')) {
            $summaries['ask_questions'] = [
                'pending' => AskQuestion::query()->where('status', AskQuestion::STATUS_PENDING)->count(),
            ];
        }

        if ($user->can('ad-requests.view')) {
            $summaries['advertising_requests'] = [
                'pending' => AdRequest::query()->where('status', 'pending')->count(),
            ];
        }

        [$subscriptionTrendLabels, $subscriptionTrendData] = $this->subscriptionTrend();

        return view('admin.dashboard', [
            'summaries' => $summaries,
            'subscriptionTrendLabels' => $subscriptionTrendLabels,
            'subscriptionTrendData' => $subscriptionTrendData,
            'hasSubscriptionTrendData' => array_sum($subscriptionTrendData) > 0,
        ]);
    }

    public function changePassword(): View
    {
        return view('admin.change-password');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', Password::min(8), 'confirmed', 'different:current_password'],
        ]);

        $user = $request->user();

        if (! $user instanceof User || ! Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors([
                'current_password' => 'The current password is incorrect.',
            ]);
        }

        $user->password = Hash::make($validated['password']);
        $user->save();

        $this->activityLogService->log(
            'users',
            'password_changed',
            $user,
            "Changed Admin user \"{$user->name}\" password.",
        );

        return redirect()
            ->route('admin.change-password')
            ->with('status', 'Password changed successfully.');
    }

    public function logout(Request $request): RedirectResponse
    {
        $this->logoutUser($request);

        return redirect()->route('admin.login');
    }

    private function logoutUser(Request $request): void
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }

    /** @return array{total: int, published: int} */
    private function contentSummary(Builder $query, string $publishedStatus): array
    {
        $summary = $query
            ->selectRaw('COUNT(*) AS total')
            ->selectRaw('SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) AS published', [$publishedStatus])
            ->first();

        return [
            'total' => (int) ($summary?->total ?? 0),
            'published' => (int) ($summary?->published ?? 0),
        ];
    }

    private function adminUsersQuery(): Builder
    {
        $guard = (string) config('admin_modules.guard', 'web');
        $adminAccess = (string) config('admin_modules.access_permission', 'admin.access');
        $systemRoles = array_keys((array) config('admin_modules.system_roles', []));

        return User::query()->whereHas('roles', function (Builder $query) use ($guard, $adminAccess, $systemRoles): void {
            $query->where('guard_name', $guard)
                ->where(function (Builder $roleQuery) use ($guard, $adminAccess, $systemRoles): void {
                    $roleQuery->where('name', 'super-admin')
                        ->orWhere(function (Builder $customRoleQuery) use ($guard, $adminAccess, $systemRoles): void {
                            $customRoleQuery
                                ->whereNotIn('name', $systemRoles)
                                ->whereHas('permissions', fn (Builder $permissionQuery) => $permissionQuery
                                    ->where('name', $adminAccess)
                                    ->where('guard_name', $guard));
                        });
                });
        });
    }

    /** @return array{0: array<int, string>, 1: array<int, int>} */
    private function subscriptionTrend(): array
    {
        $startDate = today()->subDays(29)->startOfDay();
        $endDate = today()->endOfDay();
        $countsByDate = UserSubscription::query()
            ->where('status', 'active')
            ->whereIn('product_for', [SubscriptionProduct::FOR_PLAN, SubscriptionProduct::FOR_MEMBERSHIP])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) AS subscription_date, COUNT(*) AS subscription_count')
            ->groupByRaw('DATE(created_at)')
            ->pluck('subscription_count', 'subscription_date');
        $labels = [];
        $data = [];

        for ($offset = 0; $offset < 30; $offset++) {
            $date = $startDate->copy()->addDays($offset);
            $labels[] = $date->format('d M');
            $data[] = (int) ($countsByDate[$date->toDateString()] ?? 0);
        }

        return [$labels, $data];
    }
}
