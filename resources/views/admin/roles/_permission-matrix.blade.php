@php
    $checkedPermissions = old('permissions', $selectedPermissions ?? []);
    $temporarilyHiddenPermissionModules = collect($modules)->filter(
        fn (array $module, string $moduleName): bool => in_array($module['group'] ?? null, ['Monetization'], true)
            || in_array($moduleName, ['memberships', 'ask-questions', 'magazines', 'audio', 'audios', 'author-settings', 'ad-requests', 'tags'], true),
    );
    $visiblePermissionModules = collect($modules)->except($temporarilyHiddenPermissionModules->keys());
    $hiddenSelectedPermissions = collect($checkedPermissions)->filter(
        fn (string $permissionName): bool => $temporarilyHiddenPermissionModules->keys()
            ->contains(fn (string $moduleName): bool => str_starts_with($permissionName, $moduleName.'.')),
    );
@endphp

{{-- Temporarily hidden permissions are retained on submit so existing role assignments are not revoked. --}}
@foreach ($hiddenSelectedPermissions as $permissionName)
    <input name="permissions[]" type="hidden" value="{{ $permissionName }}">
@endforeach

<section class="card admin-settings-card" data-permission-matrix>
    <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div>
            <h3>Module Permissions</h3>
            <p>Select only the actions this role should be able to perform.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <button class="btn btn-sm btn-outline-primary" type="button" data-permission-action="select">
                <i class="fa-solid fa-check-double me-1"></i>Select All Permissions
            </button>
            <button class="btn btn-sm btn-outline-secondary" type="button" data-permission-action="clear">
                <i class="fa-solid fa-xmark me-1"></i>Clear All
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="alert alert-info d-flex align-items-start gap-2" role="status">
            <i class="fa-solid fa-shield-halved mt-1"></i>
            <div><strong>Admin Access is always enabled.</strong> It cannot be removed from custom Admin roles.</div>
        </div>

        @error('permissions')
            <div class="alert alert-danger" role="alert">{{ $message }}</div>
        @enderror
        @error('permissions.*')
            <div class="alert alert-danger" role="alert">{{ $message }}</div>
        @enderror

        @foreach ($visiblePermissionModules->groupBy('group', preserveKeys: true) as $group => $groupModules)
            <div class="mb-4">
                <h4 class="h6 text-uppercase text-muted mb-3">{{ $group }}</h4>
                <div class="row g-3">
                    @foreach ($groupModules as $moduleName => $module)
                        <div class="col-xl-6" data-permission-module="{{ $moduleName }}">
                            <div class="border rounded p-3 h-100">
                                <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
                                    <strong>{{ $module['label'] }}</strong>
                                    <button class="btn btn-sm btn-outline-secondary" type="button"
                                        data-permission-action="select" data-permission-module="{{ $moduleName }}">
                                        Select Module
                                    </button>
                                </div>
                                <div class="d-flex flex-wrap gap-3">
                                    @foreach ($module['actions'] as $action => $actionLabel)
                                        @php($permissionName = $moduleName.'.'.$action)
                                        <div class="form-check">
                                            <input class="form-check-input" id="permission_{{ $moduleName }}_{{ str_replace('.', '_', $action) }}"
                                                name="permissions[]" type="checkbox" value="{{ $permissionName }}"
                                                @checked(in_array($permissionName, $checkedPermissions, true))>
                                            <label class="form-check-label"
                                                for="permission_{{ $moduleName }}_{{ str_replace('.', '_', $action) }}">
                                                {{ $actionLabel }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</section>
