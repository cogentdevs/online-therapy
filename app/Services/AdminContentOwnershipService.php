<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Validation\ValidationException;

class AdminContentOwnershipService
{
    public function __construct(private readonly AdminHierarchyService $adminHierarchyService) {}

    public function scopeQuery(Builder|Relation $query, User $admin): Builder|Relation
    {
        $allowedAdminIds = $this->adminHierarchyService->getAllowedAdminIds($admin);
        $ownerColumn = $query instanceof Relation
            ? $query->getRelated()->qualifyColumn('owner_admin_id')
            : $query->qualifyColumn('owner_admin_id');

        return $allowedAdminIds === null
            ? $query
            : $query->whereIn($ownerColumn, $allowedAdminIds);
    }

    public function canAccess(User $admin, Model $record): bool
    {
        if ($this->adminHierarchyService->isUnrestricted($admin)) {
            return true;
        }

        if ($record->getAttribute('owner_admin_id') === null) {
            return false;
        }

        return $this->adminHierarchyService
            ->getAllowedAdminIds($admin)?->contains((int) $record->getAttribute('owner_admin_id')) === true;
    }

    public function authorizeAccess(User $admin, Model $record): void
    {
        abort_unless($this->canAccess($admin, $record), 403);
    }

    /**
     * @param  class-string<Model>  $modelClass
     * @param  array<int, int>  $recordIds
     */
    public function assertRecordsAccessible(
        User $admin,
        string $modelClass,
        array $recordIds,
        string $validationField,
    ): void {
        $uniqueRecordIds = collect($recordIds)->map(fn (mixed $id): int => (int) $id)->unique()->values();

        if ($uniqueRecordIds->isEmpty()) {
            return;
        }

        $accessibleCount = $this->scopeQuery($modelClass::query(), $admin)
            ->whereKey($uniqueRecordIds)
            ->count();

        if ($accessibleCount !== $uniqueRecordIds->count()) {
            throw ValidationException::withMessages([
                $validationField => 'One or more selected records are outside your Ownership Scope.',
            ]);
        }
    }
}
