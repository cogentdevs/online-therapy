@extends('layouts.adminLayout.admin-design')

@section('title', 'Subscription Reminder Settings')

@section('content')
    <div class="container-fluid">
        <div class="admin-page-intro">
            <div>
                <span class="admin-page-eyebrow">Subscription Notifications</span>
                <h2>Subscription Reminder Settings</h2>
                <p>Configure how many days before expiry each subscription reminder should be sent.</p>
            </div>
        </div>

        @if (session('status'))
            <div class="alert alert-success" role="alert">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                <strong>Please correct the highlighted settings.</strong>
                @error('reminder_days')<div>{{ $message }}</div>@enderror
            </div>
        @endif

        <form method="POST" action="{{ route('admin.subscription-notification-settings.update') }}">
            @csrf
            @method('PUT')

            <section class="card admin-settings-card">
                <div class="card-header">
                    <h3>Reminder Schedule</h3>
                    <p>Leave a field blank or enter 0 to disable that reminder.</p>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        @foreach ([
                            'first_reminder_days' => 'First Reminder Days',
                            'second_reminder_days' => 'Second Reminder Days',
                            'third_reminder_days' => 'Third Reminder Days',
                        ] as $field => $label)
                            <div class="col-12 col-md-4">
                                <label class="form-label" for="{{ $field }}">{{ $label }}</label>
                                <input class="form-control @error($field) is-invalid @enderror" id="{{ $field }}"
                                    name="{{ $field }}" type="number" min="0" step="1"
                                    value="{{ old($field, $setting->getAttribute($field)) }}">
                                <div class="form-text">Days before subscription expiry. Blank or 0 disables this reminder.</div>
                                @error($field)<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        @endforeach

                        <div class="col-12">
                            <div class="border rounded p-3 d-flex align-items-center justify-content-between gap-3">
                                <div>
                                    <label class="form-check-label fw-semibold" for="isActive">Reminder System Active</label>
                                    <div class="form-text">Disable this to pause the complete reminder policy.</div>
                                </div>
                                <div class="form-check form-switch mb-0">
                                    <input type="hidden" name="isActive" value="0">
                                    <input class="form-check-input @error('isActive') is-invalid @enderror" id="isActive"
                                        name="isActive" type="checkbox" value="1"
                                        @checked((string) old('isActive', (int) $setting->isActive) === '1')>
                                </div>
                            </div>
                            @error('isActive')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
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
