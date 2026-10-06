@extends('layouts.adminLayout.admin-design')

@section('title', 'Author Settings')

@section('content')
    <div class="container-fluid">
        <div class="mb-4">
            <h2 class="mb-1">Author Settings</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.author.index') }}">Authors</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Author Settings</li>
                </ol>
            </nav>
        </div>

        @if (session('status'))
            <div class="alert alert-success" role="alert">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                <strong>Please correct the highlighted settings.</strong>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.author.settings.update') }}">
            @csrf
            <section class="card admin-settings-card">
                <div class="card-header">
                    <h3>Frontend Visibility Defaults</h3>
                    <p>These defaults apply to new authors and missing per-author visibility records.</p>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @foreach ($fieldLabels as $field => $label)
                            <div class="col-md-6">
                                <div class="border rounded p-3 d-flex align-items-center justify-content-between gap-3">
                                    <label class="form-check-label fw-medium" for="setting_{{ $field }}">{{ $label }}</label>
                                    <div class="form-check form-switch mb-0">
                                        <input type="hidden" name="visibility[{{ $field }}]" value="0">
                                        <input class="form-check-input @error('visibility.'.$field) is-invalid @enderror"
                                            id="setting_{{ $field }}" name="visibility[{{ $field }}]" type="checkbox"
                                            value="1" @checked((string) old('visibility.'.$field, (int) $visibilityValues[$field]) === '1')>
                                    </div>
                                </div>
                                @error('visibility.'.$field)<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

            <div class="d-flex justify-content-end">
                <button class="btn btn-primary admin-primary-button" type="submit">
                    <i class="fa-solid fa-floppy-disk me-2" aria-hidden="true"></i>Update Settings
                </button>
            </div>
        </form>
    </div>
@endsection
