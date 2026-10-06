@extends('layouts.adminLayout.admin-design')
@section('title', 'Transfer Content Ownership')
@section('content')
    <div class="container-fluid">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
            <div>
                <h2 class="mb-1">Transfer Content Ownership</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Admin Users</a></li>
                        <li class="breadcrumb-item active">Transfer Ownership</li>
                    </ol>
                </nav>
            </div>
            <a class="btn btn-outline-secondary" href="{{ route('admin.users.index') }}">
                <i class="fa-solid fa-arrow-left me-2"></i>Back to Admin Users
            </a>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                <strong>Transfer could not be completed.</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST"
            action="{{ route('admin.users.transfer-ownership.store', ['id' => $sourceAdmin->id]) }}"
            data-ownership-transfer-confirm
            data-current-owner="{{ $sourceAdmin->name }}">
            @csrf

            <div class="card admin-settings-card mb-4">
                <div class="card-header bg-white py-3">
                    <h3 class="h6 mb-1">Current Ownership</h3>
                    <p class="text-muted small mb-0">Only content directly owned by this Admin will be transferred.</p>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-lg-4">
                            <span class="text-muted small d-block">Current Owner</span>
                            <strong>{{ $sourceAdmin->name }}</strong>
                            <span class="d-block small">{{ $sourceAdmin->email }}</span>
                        </div>
                        <div class="col-sm-6 col-lg-4">
                            <span class="text-muted small d-block">Owned Magazines</span>
                            <strong class="fs-4">{{ $ownedContentCounts['magazines'] }}</strong>
                        </div>
                        <div class="col-sm-6 col-lg-4">
                            <span class="text-muted small d-block">Owned Articles</span>
                            <strong class="fs-4">{{ $ownedContentCounts['articles'] }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card admin-settings-card">
                <div class="card-header bg-white py-3">
                    <h3 class="h6 mb-1">Transfer Settings</h3>
                    <p class="text-muted small mb-0">Roles, permissions, and historical creator fields will not change.</p>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <label class="form-label" for="new_owner_id">New Owner <span class="text-danger">*</span></label>
                        <select class="form-select select2 @error('new_owner_id') is-invalid @enderror"
                            id="new_owner_id" name="new_owner_id" required>
                            <option value="">Select New Owner</option>
                            @foreach ($newOwners as $newOwner)
                                <option value="{{ $newOwner->id }}" @selected((int) old('new_owner_id') === $newOwner->id)>
                                    {{ $newOwner->name }} — {{ $newOwner->email }}
                                </option>
                            @endforeach
                        </select>
                        @error('new_owner_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <fieldset class="mb-4">
                        <legend class="form-label">Content / Responsibility to Transfer <span class="text-danger">*</span></legend>
                        <div class="row g-3">
                            <div class="col-md-6 col-xl-4">
                                <div class="form-check border rounded p-3 ps-5 h-100">
                                    <input class="form-check-input" type="checkbox" name="content_types[]"
                                        value="magazines" id="transfer_magazines"
                                        data-transfer-count="{{ $ownedContentCounts['magazines'] }}"
                                        @checked(in_array('magazines', old('content_types', []), true))>
                                    <label class="form-check-label" for="transfer_magazines">
                                        <strong>Magazines</strong>
                                        <span class="d-block text-muted small">{{ $ownedContentCounts['magazines'] }} currently owned</span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-check border rounded p-3 ps-5 h-100">
                                    <input class="form-check-input" type="checkbox" name="content_types[]"
                                        value="articles" id="transfer_articles"
                                        data-transfer-count="{{ $ownedContentCounts['articles'] }}"
                                        @checked(in_array('articles', old('content_types', []), true))>
                                    <label class="form-check-label" for="transfer_articles">
                                        <strong>Articles</strong>
                                        <span class="d-block text-muted small">{{ $ownedContentCounts['articles'] }} currently owned</span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-check border rounded p-3 ps-5 h-100">
                                    <input class="form-check-input" type="checkbox" name="content_types[]"
                                        value="child_admins" id="transfer_child_admins"
                                        data-transfer-count="{{ $ownedContentCounts['child_admins'] }}"
                                        @checked(in_array('child_admins', old('content_types', []), true))>
                                    <label class="form-check-label" for="transfer_child_admins">
                                        <strong>Child Admins</strong>
                                        <span class="d-block text-muted small">{{ $ownedContentCounts['child_admins'] }} directly assigned</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <p class="form-text mb-0 mt-3">Direct Child Admins will be reassigned to the New Owner. Their own Magazine and Article ownership will remain unchanged.</p>
                    </fieldset>

                    <div class="form-check mb-3">
                        <input type="hidden" name="deactivate_source" value="0">
                        <input class="form-check-input" type="checkbox" name="deactivate_source" value="1"
                            id="deactivate_source" @checked(old('deactivate_source'))>
                        <label class="form-check-label" for="deactivate_source">
                            Deactivate Current Owner after a successful transfer
                        </label>
                    </div>

                    <div class="form-check mb-4">
                        <input class="form-check-input @error('confirm_transfer') is-invalid @enderror" type="checkbox"
                            name="confirm_transfer" value="1" id="confirm_transfer" required
                            @checked(old('confirm_transfer'))>
                        <label class="form-check-label" for="confirm_transfer">
                            I understand that this changes the selected operational ownership and hierarchy responsibilities.
                        </label>
                        @error('confirm_transfer')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex flex-wrap gap-2">
                        <button class="btn btn-primary admin-primary-button" type="submit">
                            <i class="fa-solid fa-right-left me-2"></i>Transfer Ownership
                        </button>
                        <a class="btn btn-outline-secondary" href="{{ route('admin.users.index') }}">Cancel</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
