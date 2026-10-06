<section class="card admin-settings-card" data-role-permission-editor
    data-role-permissions-url="{{ route('admin.users.role-permissions', ['role' => '__ROLE__']) }}"
    data-initial-role="{{ old('role_id', $selectedRoleId ?? '') }}"
    data-selected-permissions='@json(array_values($selectedCustomPermissions ?? []))'>
    <div class="card-header">
        <h3>Permission Access</h3>
        <p>Use every permission from the selected role or restrict this user to a subset of that role.</p>
    </div>
    <div class="card-body">
        <div class="d-flex flex-wrap gap-4 mb-4">
            <div class="form-check">
                <input class="form-check-input" id="permission_mode_role" name="permission_mode" type="radio" value="role"
                    data-permission-mode @checked(($selectedPermissionMode ?? 'role') === 'role')>
                <label class="form-check-label" for="permission_mode_role">Full Role Permissions</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" id="permission_mode_custom" name="permission_mode" type="radio" value="custom"
                    data-permission-mode @checked(($selectedPermissionMode ?? 'role') === 'custom')>
                <label class="form-check-label" for="permission_mode_custom">Custom / Limited Permissions</label>
            </div>
        </div>
        @error('permission_mode')<div class="alert alert-danger">{{ $message }}</div>@enderror
        @error('permissions')<div class="alert alert-danger">{{ $message }}</div>@enderror
        @error('permissions.*')<div class="alert alert-danger">{{ $message }}</div>@enderror

        <p class="text-muted mb-0" data-role-preview-empty>Select a role to review its module permissions.</p>
        <div class="d-none" data-role-preview-loading>
            <i class="fa-solid fa-spinner fa-spin me-2"></i>Loading role permissions...
        </div>
        <div class="alert alert-danger d-none mb-0" role="alert" data-role-preview-error></div>
        <div class="d-none" data-role-preview-content>
            <div class="alert alert-info d-flex justify-content-between flex-wrap gap-2">
                <span><strong data-role-preview-name></strong><span class="ms-2"><i class="fa-solid fa-shield-halved me-1"></i>Admin Access is always enabled</span></span>
                <span data-role-preview-mode-help></span>
            </div>
            <div class="d-flex flex-wrap gap-2 mb-3" data-custom-permission-controls>
                <button class="btn btn-sm btn-outline-primary" type="button" data-user-permission-action="select">Select All</button>
                <button class="btn btn-sm btn-outline-secondary" type="button" data-user-permission-action="clear">Clear All</button>
            </div>
            <div class="row g-3" data-role-preview-groups></div>
        </div>
    </div>
</section>
