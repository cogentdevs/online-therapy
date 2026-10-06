<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Magazine;
use App\Models\User;
use DomainException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class AdminContentOwnershipTransferService
{
    public const TYPE_MAGAZINES = 'magazines';

    public const TYPE_ARTICLES = 'articles';

    public const TYPE_CHILD_ADMINS = 'child_admins';

    public function __construct(
        private readonly AdminHierarchyService $adminHierarchyService,
        private readonly ActivityLogService $activityLogService,
    ) {}

    public function findAuthorizedSource(User $actor, int $sourceAdminId): User
    {
        $sourceAdmin = $this->eligibleBackendAdminsQuery()->findOrFail($sourceAdminId);
        abort_unless($this->adminHierarchyService->canManageAdmin($actor, $sourceAdmin), 403);

        return $sourceAdmin;
    }

    /** @return Collection<int, User> */
    public function eligibleNewOwners(User $actor, User $sourceAdmin): Collection
    {
        $allowedAdminIds = $this->adminHierarchyService->getAllowedAdminIds($actor);

        return $this->eligibleBackendAdminsQuery()
            ->where('is_active', true)
            ->whereKeyNot($sourceAdmin->id)
            ->when($allowedAdminIds !== null, fn (Builder $query) => $query->whereKey($allowedAdminIds))
            ->orderBy('name')
            ->get();
    }

    /**
     * @param  Collection<int, User>  $visibleAdmins
     * @return Collection<int, int>
     */
    public function transferableSourceIds(User $actor, Collection $visibleAdmins): Collection
    {
        $destinationIds = $this->eligibleBackendAdminsQuery()
            ->where('is_active', true)
            ->when(
                ! $this->adminHierarchyService->isUnrestricted($actor),
                fn (Builder $query) => $query->whereKey($this->adminHierarchyService->getAllowedAdminIds($actor)),
            )
            ->pluck('id');

        return $visibleAdmins
            ->filter(fn (User $sourceAdmin): bool => ! $sourceAdmin->hasRole('super-admin')
                && $this->adminHierarchyService->canManageAdmin($actor, $sourceAdmin)
                && $destinationIds->contains(fn (int $destinationId): bool => $destinationId !== $sourceAdmin->id))
            ->pluck('id')
            ->map(fn (mixed $id): int => (int) $id)
            ->values();
    }

    /** @return array{magazines: int, articles: int, child_admins: int} */
    public function previewCounts(User $sourceAdmin): array
    {
        return [
            self::TYPE_MAGAZINES => Magazine::query()->where('owner_admin_id', $sourceAdmin->id)->count(),
            self::TYPE_ARTICLES => Article::query()->where('owner_admin_id', $sourceAdmin->id)->count(),
            self::TYPE_CHILD_ADMINS => $this->directChildAdminsQuery($sourceAdmin)->count(),
        ];
    }

    /**
     * @param  array<int, string>  $contentTypes
     * @return array{magazines: int, articles: int, child_admins: int, source_deactivated: bool}
     */
    public function transfer(
        User $actor,
        User $sourceAdmin,
        User $newOwner,
        array $contentTypes,
        bool $deactivateSource,
    ): array {
        $this->assertTransferAllowed($actor, $sourceAdmin, $newOwner, $contentTypes, $deactivateSource);

        return DB::transaction(function () use ($actor, $sourceAdmin, $newOwner, $contentTypes, $deactivateSource): array {
            $lockedSource = User::query()->lockForUpdate()->findOrFail($sourceAdmin->id);
            $lockedNewOwner = User::query()->lockForUpdate()->findOrFail($newOwner->id);
            $directChildAdmins = in_array(self::TYPE_CHILD_ADMINS, $contentTypes, true)
                ? $this->directChildAdminsQuery($lockedSource)->lockForUpdate()->get()
                : collect();
            $this->assertTransferAllowed(
                $actor,
                $lockedSource,
                $lockedNewOwner,
                $contentTypes,
                $deactivateSource,
                $directChildAdmins,
            );

            $counts = [
                self::TYPE_MAGAZINES => 0,
                self::TYPE_ARTICLES => 0,
                self::TYPE_CHILD_ADMINS => 0,
            ];

            if (in_array(self::TYPE_MAGAZINES, $contentTypes, true)) {
                $counts[self::TYPE_MAGAZINES] = Magazine::query()
                    ->where('owner_admin_id', $lockedSource->id)
                    ->toBase()
                    ->update(['owner_admin_id' => $lockedNewOwner->id]);
            }

            if (in_array(self::TYPE_ARTICLES, $contentTypes, true)) {
                $counts[self::TYPE_ARTICLES] = Article::query()
                    ->where('owner_admin_id', $lockedSource->id)
                    ->toBase()
                    ->update(['owner_admin_id' => $lockedNewOwner->id]);
            }

            if (in_array(self::TYPE_CHILD_ADMINS, $contentTypes, true)) {
                foreach ($directChildAdmins as $directChildAdmin) {
                    $directChildAdmin->parent_admin_id = $lockedNewOwner->id;
                    $directChildAdmin->save();
                    $counts[self::TYPE_CHILD_ADMINS]++;
                }
            }

            if ($deactivateSource) {
                $lockedSource->update(['is_active' => false]);
            }

            $activityLog = $this->activityLogService->log(
                'users',
                'ownership_transferred',
                $lockedSource,
                "Transferred Admin responsibilities from \"{$lockedSource->name}\" to \"{$lockedNewOwner->name}\".",
                [
                    'source_admin' => ['id' => $lockedSource->id, 'name' => $lockedSource->name],
                    'content_types' => array_values($contentTypes),
                ],
                [
                    'new_owner' => ['id' => $lockedNewOwner->id, 'name' => $lockedNewOwner->name],
                    'magazines_transferred' => $counts[self::TYPE_MAGAZINES],
                    'articles_transferred' => $counts[self::TYPE_ARTICLES],
                    'child_admins_reassigned' => $counts[self::TYPE_CHILD_ADMINS],
                    'source_deactivated' => $deactivateSource,
                ],
            );

            if ($activityLog === null) {
                throw new RuntimeException('The Ownership Transfer audit record could not be created.');
            }

            return [
                ...$counts,
                'source_deactivated' => $deactivateSource,
            ];
        });
    }

    private function assertTransferAllowed(
        User $actor,
        User $sourceAdmin,
        User $newOwner,
        array $contentTypes,
        bool $deactivateSource,
        ?Collection $directChildAdmins = null,
    ): void {
        if ($sourceAdmin->hasRole('super-admin')
            || ! $this->adminHierarchyService->isAdminHierarchyMember($sourceAdmin)
            || ! $this->adminHierarchyService->canManageAdmin($actor, $sourceAdmin)) {
            abort(403);
        }

        if ($sourceAdmin->is($newOwner)) {
            throw ValidationException::withMessages([
                'new_owner_id' => 'The New Owner must be different from the Current Owner.',
            ]);
        }

        if (! $newOwner->is_active
            || $newOwner->hasRole('super-admin')
            || ! $this->adminHierarchyService->isAdminHierarchyMember($newOwner)
            || ! $this->eligibleNewOwners($actor, $sourceAdmin)->contains('id', $newOwner->id)) {
            throw ValidationException::withMessages([
                'new_owner_id' => 'The selected New Owner is not eligible for this Ownership Transfer.',
            ]);
        }

        $reassignsChildAdmins = in_array(self::TYPE_CHILD_ADMINS, $contentTypes, true);

        if ($reassignsChildAdmins) {
            $this->assertChildAdminReassignmentIsSafe(
                $directChildAdmins ?? $this->directChildAdminsQuery($sourceAdmin)->get(),
                $newOwner,
            );
        }

        if ($deactivateSource && $actor->is($sourceAdmin)) {
            throw ValidationException::withMessages([
                'deactivate_source' => 'You cannot deactivate your own Admin account.',
            ]);
        }

        if ($deactivateSource
            && ! $reassignsChildAdmins
            && $this->adminHierarchyService->hasActiveChildAdmins($sourceAdmin)) {
            throw ValidationException::withMessages([
                'deactivate_source' => 'The Current Owner cannot be deactivated while active Child Admins are assigned.',
            ]);
        }
    }

    /** @param Collection<int, User> $directChildAdmins */
    private function assertChildAdminReassignmentIsSafe(Collection $directChildAdmins, User $newOwner): void
    {
        try {
            foreach ($directChildAdmins as $directChildAdmin) {
                $this->adminHierarchyService->assertValidParent($directChildAdmin, $newOwner);
            }
        } catch (DomainException $exception) {
            throw ValidationException::withMessages([
                'new_owner_id' => 'The selected New Owner would create an invalid Admin Hierarchy relationship.',
            ]);
        }
    }

    private function directChildAdminsQuery(User $sourceAdmin): Builder
    {
        return $this->eligibleBackendAdminsQuery()->where('parent_admin_id', $sourceAdmin->id);
    }

    private function eligibleBackendAdminsQuery(): Builder
    {
        $guardName = (string) config('admin_modules.guard', 'web');
        $systemRoleNames = array_keys((array) config('admin_modules.system_roles', []));

        return User::query()
            ->whereDoesntHave('roles', fn (Builder $query) => $query
                ->where('guard_name', $guardName)
                ->whereIn('name', $systemRoleNames))
            ->whereHas('roles', fn (Builder $query) => $query
                ->where('guard_name', $guardName)
                ->whereNotIn('name', $systemRoleNames)
                ->whereHas('permissions', fn (Builder $permissionQuery) => $permissionQuery
                    ->where('name', (string) config('admin_modules.access_permission'))
                    ->where('guard_name', $guardName)));
    }
}
