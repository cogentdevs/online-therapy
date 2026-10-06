@extends('layouts.adminLayout.admin-design')

@section('title', 'Card Title Position')

@section('content')
    <div class="container-fluid">
        <div class="mb-4">
            <h2 class="mb-1">Card Title Position</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.home-cards.index') }}">Home Cards</a></li>
                    <li class="breadcrumb-item active">Card Title Position</li>
                </ol>
            </nav>
        </div>

        @if (session('status'))
            <div class="alert alert-success" role="alert">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger"><strong>Please correct the highlighted fields.</strong></div>
        @endif

        <form method="POST" action="{{ route('admin.home-cards.card-title-position.update') }}">
            @csrf
            @method('PUT')
            <section class="card admin-settings-card">
                <div class="card-header">
                    <h3>Home Card Titles</h3>
                    <p>Manage card-title alignment for each homepage Home Card area.</p>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        @foreach ([
                            'below_slider_title_position' => ['Below Slider', $belowSliderTitlePosition],
                            'above_footer_title_position' => ['Above Footer', $aboveFooterTitlePosition],
                        ] as $field => [$label, $currentPosition])
                            <div class="col-lg-6">
                                <label class="form-label" for="{{ $field }}">{{ $label }}</label>
                                <select class="form-select select2 @error($field) is-invalid @enderror"
                                    id="{{ $field }}" name="{{ $field }}" required>
                                    @foreach (['center' => 'Center', 'left' => 'Left', 'right' => 'Right'] as $value => $option)
                                        <option value="{{ $value }}" @selected(old($field, $currentPosition) === $value)>
                                            {{ $option }}
                                        </option>
                                    @endforeach
                                </select>
                                @error($field)
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
            <div class="d-flex justify-content-end gap-2 mt-4">
                <a class="btn btn-outline-secondary" href="{{ route('admin.home-cards.index') }}">Cancel</a>
                <button class="btn btn-primary admin-primary-button" type="submit">
                    <i class="fa-solid fa-floppy-disk me-2"></i>Update Settings
                </button>
            </div>
        </form>
    </div>
@endsection
