<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Throwable;

class ActivityLogService
{
    /** @var array<int, string> */
    private const SENSITIVE_KEYS = [
        'password',
        'password_confirmation',
        'remember_token',
        'token',
        'api_token',
        'access_token',
        'refresh_token',
        'secret',
        'secret_key',
        'credentials',
        'account_no',
        'iban',
        'payment_slip',
    ];

    /** @var array<int, string> */
    private const NOISE_KEYS = [
        'created_at',
        'updated_at',
        'created_by',
        'updated_by',
        'published_by',
    ];

    /**
     * @param  array<string, mixed>|null  $oldValues
     * @param  array<string, mixed>|null  $newValues
     */
    public function log(
        string $module,
        string $action,
        Model|string|null $subject,
        string $description,
        ?array $oldValues = null,
        ?array $newValues = null,
    ): ?ActivityLog {
        $actor = auth()->user();

        if (! $actor instanceof User || ! $this->isAdminRequest() || ! $this->isAdminActor($actor)) {
            return null;
        }

        return $this->store($actor, $module, $action, $subject, $description, $oldValues, $newValues);
    }

    /**
     * @param  array<string, mixed>|null  $oldValues
     * @param  array<string, mixed>|null  $newValues
     */
    public function logUserLifecycle(
        User $user,
        string $action,
        Model $subject,
        string $description,
        ?array $oldValues = null,
        ?array $newValues = null,
    ): ?ActivityLog {
        $actor = auth('web')->user();

        if (! $actor instanceof User || ! $actor->is($user)) {
            return null;
        }

        return $this->store(
            $actor,
            'user_subscriptions',
            $action,
            $subject,
            $description,
            $oldValues,
            $newValues,
            $actor->roles()->where('guard_name', 'web')->orderBy('name')->value('name'),
        );
    }

    /** @param array<string, mixed> $filters */
    public function filteredQuery(array $filters): Builder
    {
        return ActivityLog::query()
            ->when(Arr::get($filters, 'from_date'), fn (Builder $query, string $date): Builder => $query->whereDate('created_at', '>=', $date))
            ->when(Arr::get($filters, 'to_date'), fn (Builder $query, string $date): Builder => $query->whereDate('created_at', '<=', $date))
            ->when(Arr::get($filters, 'user_id'), fn (Builder $query, int|string $userId): Builder => $query->where('user_id', $userId))
            ->when(Arr::get($filters, 'role'), fn (Builder $query, string $role): Builder => $query->where('role_name', $role))
            ->when(Arr::get($filters, 'module'), fn (Builder $query, string $module): Builder => $query->where('module', $module))
            ->when(Arr::get($filters, 'action'), fn (Builder $query, string $action): Builder => $query->where('action', $action));
    }

    /** @param array<string, mixed>|null $values */
    public function sanitize(?array $values): ?array
    {
        if ($values === null) {
            return null;
        }

        $sanitized = collect(Arr::except($values, [...self::SENSITIVE_KEYS, ...self::NOISE_KEYS]))
            ->reject(fn (mixed $value, string $key): bool => $this->isSensitiveKey($key))
            ->map(fn (mixed $value): mixed => $this->sanitizeValue($value))
            ->all();

        return $sanitized === [] ? null : $sanitized;
    }

    private function isAdminRequest(): bool
    {
        return Str::startsWith((string) request()->route()?->getName(), 'admin.');
    }

    private function isAdminActor(User $actor): bool
    {
        return $actor->hasRole('super-admin') || $actor->can((string) config('admin_modules.access_permission'));
    }

    private function roleSnapshot(User $actor): ?string
    {
        if ($actor->hasRole('super-admin')) {
            return 'super-admin';
        }

        return $actor->roles()
            ->where('guard_name', (string) config('admin_modules.guard', 'web'))
            ->whereHas('permissions', fn (Builder $query): Builder => $query->where('name', (string) config('admin_modules.access_permission')))
            ->value('name');
    }

    private function isSensitiveKey(string $key): bool
    {
        return Str::contains(Str::lower($key), ['password', 'token', 'secret', 'credential', 'api_key', 'private_key']);
    }

    /**
     * @param  array<string, mixed>|null  $oldValues
     * @param  array<string, mixed>|null  $newValues
     */
    private function store(
        User $actor,
        string $module,
        string $action,
        Model|string|null $subject,
        string $description,
        ?array $oldValues,
        ?array $newValues,
        ?string $roleName = null,
    ): ?ActivityLog {
        try {
            return ActivityLog::query()->create([
                'user_id' => $actor->id,
                'user_name' => $actor->name,
                'role_name' => $roleName ?? $this->roleSnapshot($actor),
                'module' => $module,
                'action' => $action,
                'subject_type' => $subject instanceof Model ? $subject::class : $subject,
                'subject_id' => $subject instanceof Model ? $subject->getKey() : null,
                'description' => Str::limit(Str::squish($description), 1000, ''),
                'old_values' => $this->sanitize($oldValues),
                'new_values' => $this->sanitize($newValues),
                'ip_address' => request()->ip(),
                'user_agent' => Str::limit((string) request()->userAgent(), 2000, ''),
            ]);
        } catch (Throwable $exception) {
            report($exception);

            return null;
        }
    }

    private function sanitizeValue(mixed $value): mixed
    {
        if (is_string($value)) {
            return Str::limit($value, 1000, '…');
        }

        if (is_array($value)) {
            return collect($value)
                ->take(50)
                ->map(fn (mixed $nestedValue): mixed => $this->sanitizeValue($nestedValue))
                ->all();
        }

        if (is_object($value)) {
            return method_exists($value, '__toString') ? Str::limit((string) $value, 1000, '…') : get_debug_type($value);
        }

        return $value;
    }
}
