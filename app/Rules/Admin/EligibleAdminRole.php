<?php

namespace App\Rules\Admin;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Translation\PotentiallyTranslatedString;
use Spatie\Permission\Models\Role;

class EligibleAdminRole implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $isEligible = Role::query()
            ->whereKey($value)
            ->where('guard_name', (string) config('admin_modules.guard', 'web'))
            ->whereNotIn('name', array_keys((array) config('admin_modules.system_roles', [])))
            ->whereHas('permissions', fn (Builder $query) => $query
                ->where('name', (string) config('admin_modules.access_permission'))
                ->where('guard_name', (string) config('admin_modules.guard', 'web')))
            ->exists();

        if (! $isEligible) {
            $fail('The selected role is not an eligible Admin role.');
        }
    }
}
