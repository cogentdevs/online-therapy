@extends('layouts.adminLayout.admin-design')

@section('title', 'Add Author')

@section('content')
    <div class="container-fluid">
        <div class="mb-4">
            <h2 class="mb-1">Add Author</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.author.index') }}">Authors</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Add Author</li>
                </ol>
            </nav>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger" role="alert"><strong>Please correct the highlighted fields.</strong></div>
        @endif

        <form method="POST" action="{{ route('admin.author.store') }}" enctype="multipart/form-data">
            @csrf
            <section class="card admin-settings-card">
                <div class="card-header">
                    <h3>Author Information</h3>
                    <p>All author information is optional.</p>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-lg-6">
                            <label class="form-label" for="name">Name</label>
                            <input class="form-control @error('name') is-invalid @enderror" id="name" name="name"
                                type="text" value="{{ old('name') }}" maxlength="255">
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-lg-6">
                            <label class="form-label" for="contact_number">Contact Number</label>
                            <input class="form-control @error('contact_number') is-invalid @enderror" id="contact_number"
                                name="contact_number" type="text" value="{{ old('contact_number') }}" maxlength="50">
                            @error('contact_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-lg-6">
                            <label class="form-label" for="email">Email</label>
                            <input class="form-control @error('email') is-invalid @enderror" id="email" name="email"
                                type="email" value="{{ old('email') }}" maxlength="255">
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-lg-6">
                            <label class="form-label" for="qualification">Qualification</label>
                            <input class="form-control @error('qualification') is-invalid @enderror" id="qualification"
                                name="qualification" type="text" value="{{ old('qualification') }}" maxlength="255">
                            @error('qualification')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="experience_detail">Experience Detail</label>
                            <textarea class="form-control @error('experience_detail') is-invalid @enderror" id="experience_detail"
                                name="experience_detail" rows="4">{{ old('experience_detail') }}</textarea>
                            @error('experience_detail')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-lg-6">
                            <label class="form-label" for="experience_years">Experience Years</label>
                            <input class="form-control @error('experience_years') is-invalid @enderror" id="experience_years"
                                name="experience_years" type="number" value="{{ old('experience_years') }}" min="0">
                            @error('experience_years')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-lg-6">
                            <label class="form-label" for="speciality">Speciality</label>
                            <input class="form-control @error('speciality') is-invalid @enderror" id="speciality"
                                name="speciality" type="text" value="{{ old('speciality') }}" maxlength="255">
                            @error('speciality')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-lg-6">
                            <label class="form-label" for="picture">Picture</label>
                            <input class="form-control @error('picture') is-invalid @enderror" id="picture" name="picture"
                                type="file" accept=".png,.jpg,.jpeg,.webp,image/png,image/jpeg,image/webp">
                            <div class="form-text">PNG, JPG, JPEG, or WEBP. Maximum 5 MB.</div>
                            @error('picture')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </section>

            <section class="card admin-settings-card">
                <div class="card-header">
                    <h3>Frontend Visibility</h3>
                    <p>Defaults come from Author Settings and can be changed for this author.</p>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @foreach ($fieldLabels as $field => $label)
                            <div class="col-md-6 col-xl-3">
                                <div class="form-check border rounded p-3 ps-5 h-100">
                                    <input type="hidden" name="visibility[{{ $field }}]" value="0">
                                    <input class="form-check-input @error('visibility.'.$field) is-invalid @enderror"
                                        id="visibility_{{ $field }}" name="visibility[{{ $field }}]" type="checkbox"
                                        value="1" @checked((string) old('visibility.'.$field, (int) $visibilityValues[$field]) === '1')>
                                    <label class="form-check-label" for="visibility_{{ $field }}">{{ $label }}</label>
                                    @error('visibility.'.$field)<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

            <div class="d-flex justify-content-end gap-2">
                <a class="btn btn-outline-secondary" href="{{ route('admin.author.index') }}">Cancel</a>
                <button class="btn btn-primary admin-primary-button" type="submit">
                    <i class="fa-solid fa-plus me-2" aria-hidden="true"></i>Add Author
                </button>
            </div>
        </form>
    </div>
@endsection
