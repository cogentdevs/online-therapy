<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use App\Rules\Admin\EligibleAdminRole;
use App\Services\Authorization\AdminUserPermissionService;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Validator;
use Spatie\Permission\Models\Role;

class StoreAdminUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique((new User)->getTable(), 'email')],
            'phone' => ['nullable', 'string', 'max:50'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'role_id' => ['required', 'integer', new EligibleAdminRole],
            'permission_mode' => ['required', Rule::in([
                AdminUserPermissionService::MODE_ROLE,
                AdminUserPermissionService::MODE_CUSTOM,
            ])],
            'permissions' => ['present', 'array'],
            'permissions.*' => ['string', 'distinct', Rule::in(app(AdminUserPermissionService::class)->registeredPermissionNames())],
            'is_active' => ['required', 'boolean'],
        ];
    }

    /** @return array<int, callable> */
    public function after(): array
    {
        return [function (Validator $validator): void {
            if ($validator->errors()->has('role_id') || $validator->errors()->has('permissions')) {
                return;
            }

            $role = Role::query()->find($this->integer('role_id'));
            $allowed = $role ? app(AdminUserPermissionService::class)->assignableRolePermissionNames($role) : collect();
            $invalid = collect($this->input('permissions', []))->diff($allowed);

            if ($invalid->isNotEmpty()) {
                $validator->errors()->add('permissions', 'Selected permissions must belong to the selected Admin role.');
            }

            $actor = $this->user();

            if (! $actor instanceof User || ! $role) {
                return;
            }

            $permissionService = app(AdminUserPermissionService::class);

            if (! $permissionService->canAssignRole($actor, $role)) {
                $validator->errors()->add('role_id', 'Selected role includes permissions outside your allowed permission scope.');
            }

            if ($this->input('permission_mode') === AdminUserPermissionService::MODE_CUSTOM
                && ! $permissionService->permissionsWithinActorScope($actor, (array) $this->input('permissions', []))) {
                $validator->errors()->add('permissions', 'Selected permissions include actions outside your allowed permission scope.');
            }
        }];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => Str::squish((string) $this->input('name')),
            'email' => Str::lower(trim((string) $this->input('email'))),
            'phone' => filled($this->input('phone')) ? trim((string) $this->input('phone')) : null,
            'permission_mode' => $this->input('permission_mode', AdminUserPermissionService::MODE_ROLE),
            'permissions' => array_values(array_unique((array) $this->input('permissions', []))),
        ]);
    }
}
