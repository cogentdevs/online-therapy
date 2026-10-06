<?php

namespace App\Http\Requests\Admin;

use App\Services\Authorization\PermissionSyncService;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use Spatie\Permission\Models\Role;

class UpdateRoleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $role = Role::query()
            ->where('guard_name', app(PermissionSyncService::class)->guardName())
            ->find((int) $this->route('id'));

        return ! $role || ! in_array($role->name, array_keys((array) config('admin_modules.system_roles', [])), true);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $guard = app(PermissionSyncService::class)->guardName();

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::notIn(array_keys((array) config('admin_modules.system_roles', []))),
                Rule::unique(config('permission.table_names.roles', 'roles'), 'name')
                    ->where(fn (Builder $query) => $query->where('guard_name', $guard))
                    ->ignore((int) $this->route('id')),
            ],
            'permissions' => ['sometimes', 'array'],
            'permissions.*' => [
                'string',
                'distinct',
                Rule::in(app(PermissionSyncService::class)->registeredPermissions()->all()),
                Rule::exists(config('permission.table_names.permissions', 'permissions'), 'name')
                    ->where(fn (Builder $query) => $query->where('guard_name', $guard)),
            ],
        ];
    }

    /** @return array<int, callable(Validator): void> */
    public function after(): array
    {
        return [function (Validator $validator): void {
            $systemRoleNames = collect(array_keys((array) config('admin_modules.system_roles', [])))
                ->map(fn (string $roleName) => Str::lower($roleName));

            if ($systemRoleNames->contains(Str::lower((string) $this->input('name')))) {
                $validator->errors()->add('name', 'This is a protected system role name.');
            }
        }];
    }

    protected function prepareForValidation(): void
    {
        $name = $this->input('name');

        if (is_string($name)) {
            $this->merge(['name' => Str::squish($name)]);
        }
    }
}
