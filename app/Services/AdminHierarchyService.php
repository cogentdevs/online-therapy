<?php

namespace App\Services;

use App\Models\User;
use DomainException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class AdminHierarchyService
{
    /** @return Collection<int, int> */
    public function getDescendantIds(User $admin): Collection
    {
        $adminRows = $this->adminHierarchyRows();

        if (! $adminRows->contains('id', $admin->getKey())) {
            return collect();
        }

        return $this->descendantIdsFromRows($admin->getKey(), $adminRows);
    }

    /**
     * A null result represents unrestricted Super Admin access.
     *
     * @return Collection<int, int>|null
     */
    public function getAllowedAdminIds(User $admin): ?Collection
    {
        if ($this->isUnrestricted($admin)) {
            return null;
        }

        $adminRows = $this->adminHierarchyRows();

        if (! $adminRows->contains('id', $admin->getKey())) {
            return collect();
        }

        return $this->descendantIdsFromRows($admin->getKey(), $adminRows)
            ->prepend((int) $admin->getKey())
            ->values();
    }

    public function isDescendantOf(User $candidate, User $parent): bool
    {
        if ($candidate->is($parent)) {
            return false;
        }

        return $this->getDescendantIds($parent)->contains((int) $candidate->getKey());
    }

    public function canManageAdmin(User $actor, User $target): bool
    {
        if (! $this->isAdminHierarchyMember($target)) {
            return false;
        }

        if ($this->isUnrestricted($actor)) {
            return true;
        }

        return $this->getAllowedAdminIds($actor)?->contains((int) $target->getKey()) === true;
    }

    public function isUnrestricted(User $admin): bool
    {
        return $admin->hasRole('super-admin');
    }

    public function isAdminHierarchyMember(User $user): bool
    {
        if ($this->isUnrestricted($user)) {
            return true;
        }

        if ($user->hasAnyRole(['user', 'consultant'])) {
            return false;
        }

        return $user->roles()
            ->where('guard_name', $this->guardName())
            ->whereNotIn('name', $this->systemRoleNames())
            ->whereHas('permissions', fn (Builder $query) => $query
                ->where('name', $this->adminAccessPermission())
                ->where('guard_name', $this->guardName()))
            ->exists();
    }

    public function assertValidParent(User $admin, User $parentAdmin): void
    {
        if (! $admin->exists || ! $parentAdmin->exists) {
            throw new DomainException('Both Admin users must exist before assigning a Parent Admin.');
        }

        if ($this->isUnrestricted($admin)) {
            throw new DomainException('The Super Admin must remain the root of the Admin Hierarchy.');
        }

        if (! $this->isAdminHierarchyMember($admin)) {
            throw new DomainException('The selected user is not an Admin Hierarchy member.');
        }

        if (! $this->isAdminHierarchyMember($parentAdmin)) {
            throw new DomainException('The selected Parent Admin is not an Admin Hierarchy member.');
        }

        if ($admin->is($parentAdmin)) {
            throw new DomainException('An Admin cannot be its own Parent Admin.');
        }

        if ($this->isDescendantOf($parentAdmin, $admin)) {
            throw new DomainException('An Admin cannot be assigned under one of its own Descendant Admins.');
        }
    }

    public function hasChildAdmins(User $admin): bool
    {
        return $this->adminHierarchyRows()
            ->contains(fn (User $candidate): bool => (int) $candidate->parent_admin_id === (int) $admin->getKey());
    }

    public function hasActiveChildAdmins(User $admin): bool
    {
        return $this->adminHierarchyRows(activeOnly: true)
            ->contains(fn (User $candidate): bool => (int) $candidate->parent_admin_id === (int) $admin->getKey());
    }

    /** @return Collection<int, User> */
    private function adminHierarchyRows(bool $activeOnly = false): Collection
    {
        $systemRoleNames = $this->systemRoleNames();

        return User::query()
            ->select(['id', 'parent_admin_id'])
            ->where(function (Builder $query) use ($systemRoleNames): void {
                $query->whereHas('roles', fn (Builder $roleQuery) => $roleQuery
                    ->where('name', 'super-admin')
                    ->where('guard_name', $this->guardName()))
                    ->orWhere(function (Builder $customAdminQuery) use ($systemRoleNames): void {
                        $customAdminQuery
                            ->whereDoesntHave('roles', fn (Builder $roleQuery) => $roleQuery
                                ->where('guard_name', $this->guardName())
                                ->whereIn('name', ['user', 'consultant']))
                            ->whereHas('roles', fn (Builder $roleQuery) => $roleQuery
                                ->where('guard_name', $this->guardName())
                                ->whereNotIn('name', $systemRoleNames)
                                ->whereHas('permissions', fn (Builder $permissionQuery) => $permissionQuery
                                    ->where('name', $this->adminAccessPermission())
                                    ->where('guard_name', $this->guardName())));
                    });
            })
            ->when($activeOnly, fn (Builder $query) => $query->where('is_active', true))
            ->get();
    }

    /**
     * @param  Collection<int, User>  $adminRows
     * @return Collection<int, int>
     */
    private function descendantIdsFromRows(int $adminId, Collection $adminRows): Collection
    {
        $childrenByParent = $adminRows
            ->filter(fn (User $user): bool => $user->parent_admin_id !== null)
            ->groupBy(fn (User $user): int => (int) $user->parent_admin_id);
        $descendantIds = collect();
        $pendingAdminIds = collect([(int) $adminId]);
        $visitedAdminIds = collect([(int) $adminId => true]);

        while ($pendingAdminIds->isNotEmpty()) {
            $currentAdminId = (int) $pendingAdminIds->shift();

            foreach ($childrenByParent->get($currentAdminId, collect()) as $childAdmin) {
                $childAdminId = (int) $childAdmin->getKey();

                if ($visitedAdminIds->has($childAdminId)) {
                    continue;
                }

                $visitedAdminIds->put($childAdminId, true);
                $descendantIds->push($childAdminId);
                $pendingAdminIds->push($childAdminId);
            }
        }

        return $descendantIds->values();
    }

    /** @return array<int, string> */
    private function systemRoleNames(): array
    {
        return array_keys((array) config('admin_modules.system_roles', []));
    }

    private function adminAccessPermission(): string
    {
        return (string) config('admin_modules.access_permission', 'admin.access');
    }

    private function guardName(): string
    {
        return (string) config('admin_modules.guard', 'web');
    }
}
