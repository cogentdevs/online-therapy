@extends('layouts.adminLayout.admin-design')

@section('title', 'Home Headings')

@section('content')
    <div class="container-fluid">
        <div class="mb-4">
            <h2 class="mb-1">Home Headings</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Home Headings</li>
                </ol>
            </nav>
        </div>

        @if (session('status'))
            <div class="alert alert-success" role="alert">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                <strong>Please correct the highlighted fields.</strong>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.home-headings.update') }}">
            @csrf
            @method('PUT')

            <section class="card admin-settings-card">
                <div class="card-header">
                    <h3>Homepage Section Headings</h3>
                    <p>Manage Urdu heading content and position for the major homepage sections.</p>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        @foreach ($sections as $sectionName => $configuration)
                            @php
                                $heading = $headings->get($sectionName);
                            @endphp
                            <div class="col-12">
                                <div class="border rounded p-3">
                                    <h4 class="h6 mb-3">{{ $configuration['label'] }}</h4>
                                    <div class="row g-3">
                                        @foreach ($configuration['fields'] as $field)
                                            @php
                                                $fieldName = "sections.{$sectionName}.{$field}";
                                                $fieldId = "{$sectionName}_{$field}";
                                                $fieldLabel = match ($field) {
                                                    'short_title' => 'Short Title',
                                                    'main_title' => 'Main Title',
                                                    'short_detail' => 'Short Detail',
                                                    default => 'Content Position',
                                                };
                                            @endphp
                                            <div class="col-md-6">
                                                <label class="form-label" for="{{ $fieldId }}">{{ $fieldLabel }}</label>
                                                @if ($field === 'content_position')
                                                    <select
                                                        class="form-select select2 @error($fieldName) is-invalid @enderror"
                                                        id="{{ $fieldId }}"
                                                        name="sections[{{ $sectionName }}][{{ $field }}]">
                                                        <option value="">Select Position</option>
                                                        @foreach (['center' => 'Center', 'left' => 'Left', 'right' => 'Right'] as $value => $label)
                                                            <option value="{{ $value }}"
                                                                @selected(old($fieldName, $heading?->getAttribute($field)) === $value)>{{ $label }}</option>
                                                        @endforeach
                                                    </select>
                                                @else
                                                    <input
                                                        class="form-control @error($fieldName) is-invalid @enderror"
                                                        id="{{ $fieldId }}"
                                                        name="sections[{{ $sectionName }}][{{ $field }}]" type="text"
                                                        value="{{ old($fieldName, $heading?->getAttribute($field)) }}"
                                                        maxlength="255">
                                                @endif
                                                @error($fieldName)
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

            <div class="d-flex justify-content-end">
                <button class="btn btn-primary admin-primary-button" type="submit">
                    <i class="fa-solid fa-floppy-disk me-2" aria-hidden="true"></i>Update Headings
                </button>
            </div>
        </form>
    </div>
@endsection
