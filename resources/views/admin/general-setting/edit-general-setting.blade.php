@extends('layouts.adminLayout.admin-design')

@section('title', 'General Settings')

@section('content')
    <div class="container-fluid">
        <div class="admin-page-intro">
            <div>
                <span class="admin-page-eyebrow">Website Configuration</span>
                <h2>General Settings</h2>
                <p>Manage website identity, contact details, social profiles, and system preferences from one place.</p>
            </div>
        </div>

        @if (session('status'))
            <div class="alert alert-success" role="alert">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('admin.general-setting.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <section class="card admin-settings-card">
                <div class="card-header">
                    <h3>Website Information</h3>
                    <p>Core website identity and branding assets.</p>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-lg-4">
                            <label for="app_name" class="form-label">Website Name</label>
                            <input id="app_name" type="text" name="app_name"
                                value="{{ old('app_name', $generalSetting->app_name) }}"
                                class="form-control @error('app_name') is-invalid @enderror">
                            @error('app_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-lg-4">
                            <label for="url" class="form-label">Website URL</label>
                            <input id="url" type="url" name="url"
                                value="{{ old('url', $generalSetting->url) }}" placeholder="https://example.com"
                                class="form-control @error('url') is-invalid @enderror">
                            @error('url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-lg-4">
                            <label for="google_ads_client_id" class="form-label">Google Ads Client ID</label>
                            <input id="google_ads_client_id" type="text" name="google_ads_client_id"
                                value="{{ old('google_ads_client_id', $generalSetting->google_ads_client_id) }}"
                                placeholder="ca-pub-1234567890123456"
                                class="form-control @error('google_ads_client_id') is-invalid @enderror">
                            @error('google_ads_client_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        @foreach ([
            'logo' => ['Header/App Logo', 'Recommended size: 270 × 100 px.'],
            'footer_logo' => ['Footer Logo', 'Recommended size: 270 × 100 px.'],
            'favicon' => ['Favicon', 'Recommended size: 30 × 30 px.'],
        ] as $field => [$label, $help])
                            <div class="col-lg-4">
                                <label for="{{ $field }}" class="form-label">{{ $label }}</label>
                                <div class="admin-image-preview">
                                    @if ($imagePaths[$field])
                                        <img src="{{ asset($imagePaths[$field]) }}"
                                            alt="Current {{ strtolower($label) }}">
                                    @else
                                        <span>No image uploaded</span>
                                    @endif
                                </div>
                                <input id="{{ $field }}" type="file" name="{{ $field }}"
                                    accept=".png,.jpg,.jpeg,.webp,image/png,image/jpeg,image/webp"
                                    class="form-control @error($field) is-invalid @enderror">
                                <div class="form-text">{{ $help }} Maximum file size:
                                    {{ $field === 'favicon' ? '2 MB' : '4 MB' }}.</div>
                                @error($field)
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        @endforeach
                        <div class="col-12">
                            <label for="footer_text" class="form-label">Footer Text</label>
                            <textarea id="footer_text" name="footer_text" rows="3"
                                class="form-control @error('footer_text') is-invalid @enderror">{{ old('footer_text', $generalSetting->footer_text) }}</textarea>
                            @error('footer_text')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </section>

            <section class="card admin-settings-card">
                <div class="card-header">
                    <h3>Contact Information</h3>
                    <p>Public contact channels displayed across the website.</p>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-lg-6">
                            <label for="contact_1" class="form-label">Contact 1</label>
                            <input id="contact_1" type="text" name="contact_1"
                                value="{{ old('contact_1', $generalSetting->contact_1) }}"
                                class="form-control @error('contact_1') is-invalid @enderror">
                            @error('contact_1')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-lg-6">
                            <label for="contact_2" class="form-label">Contact 2</label>
                            <input id="contact_2" type="text" name="contact_2"
                                value="{{ old('contact_2', $generalSetting->contact_2) }}"
                                class="form-control @error('contact_2') is-invalid @enderror">
                            @error('contact_2')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-lg-6">
                            <label for="email" class="form-label">Email</label>
                            <input id="email" type="email" name="email"
                                value="{{ old('email', $generalSetting->email) }}"
                                class="form-control @error('email') is-invalid @enderror">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12">
                            <label for="address" class="form-label">Address</label>
                            <textarea id="address" name="address" rows="3" class="form-control @error('address') is-invalid @enderror">{{ old('address', $generalSetting->address) }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </section>

            <section class="card admin-settings-card">
                <div class="card-header">
                    <h3>Social Links</h3>
                    <p>Leave any profile blank when it should not appear on the website.</p>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        @foreach ([
            'facebook' => 'Facebook',
            'instagram' => 'Instagram',
            'youtube' => 'YouTube',
            'linkedin' => 'LinkedIn',
            'tiktok' => 'TikTok',
            'x' => 'X / Twitter',
        ] as $field => $label)
                            <div class="col-lg-6">
                                <label for="{{ $field }}" class="form-label">{{ $label }}</label>
                                <input id="{{ $field }}" type="url" name="{{ $field }}"
                                    value="{{ old($field, $generalSetting->getAttribute($field)) }}"
                                    placeholder="https://" class="form-control @error($field) is-invalid @enderror">
                                @error($field)
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

            <section class="card admin-settings-card">
                <div class="card-header">
                    <h3>App Info</h3>
                    <p>Manage application section content and mobile store links.</p>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-lg-6">
                            <label for="app_section_heading" class="form-label">App Section Heading</label>
                            <input id="app_section_heading" type="text" name="app_section_heading"
                                value="{{ old('app_section_heading', $generalSetting->app_section_heading) }}"
                                class="form-control @error('app_section_heading') is-invalid @enderror">
                            @error('app_section_heading')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-lg-6">
                            <label for="app_section_text" class="form-label">App Section Text</label>
                            <textarea id="app_section_text" name="app_section_text" rows="3"
                                class="form-control @error('app_section_text') is-invalid @enderror">{{ old('app_section_text', $generalSetting->app_section_text) }}</textarea>
                            @error('app_section_text')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        @foreach ([
            'play_store' => ['Play Store', 'https://play.google.com/...'],
            'app_store' => ['App Store', 'https://apps.apple.com/...'],
        ] as $store => [$label, $placeholder])
                            <div class="col-lg-6">
                                <label for="{{ $store }}_icon" class="form-label">{{ $label }}
                                    Icon</label>
                                <div class="admin-image-preview">
                                    @if ($imagePaths[$store . '_icon'])
                                        <img src="{{ asset($imagePaths[$store . '_icon']) }}"
                                            alt="Current {{ strtolower($label) }} icon">
                                    @else
                                        <span>No image uploaded</span>
                                    @endif
                                </div>
                                <input id="{{ $store }}_icon" type="file" name="{{ $store }}_icon"
                                    accept=".png,.jpg,.jpeg,.webp,image/png,image/jpeg,image/webp"
                                    class="form-control @error($store . '_icon') is-invalid @enderror">
                                <div class="form-text">Recommended size: 160 × 60 px. Maximum file size: 4 MB.</div>
                                @error($store . '_icon')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-lg-6">
                                <label for="{{ $store }}_link" class="form-label">{{ $label }}
                                    Link</label>
                                <input id="{{ $store }}_link" type="url" name="{{ $store }}_link"
                                    value="{{ old($store . '_link', $generalSetting->getAttribute($store . '_link')) }}"
                                    placeholder="{{ $placeholder }}"
                                    class="form-control @error($store . '_link') is-invalid @enderror">
                                @error($store . '_link')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

            <section class="card admin-settings-card">
                <div class="card-header">
                    <h3>System Settings</h3>
                    <p>Control account and session limits.</p>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        {{-- Default language is retained internally; fixed English has no Admin selector. --}}
                        @if (false)
                        <div class="d-none">
                            <label for="default_language_id" class="form-label">Default Language</label>
                            <select id="default_language_id" name="default_language_id"
                                class="form-select select2 @error('default_language_id') is-invalid @enderror">
                                <option value="">Select Default Language</option>
                                @foreach ($activeLanguages as $language)
                                    <option value="{{ $language->id }}" @selected((string) old('default_language_id', $generalSetting->default_language_id) === (string) $language->id)>
                                        {{ $language->name ?? $language->code }}
                                    </option>
                                @endforeach
                            </select>
                            @error('default_language_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        @endif
                        <div class="col-lg-4">
                            <label for="max_devices_per_user" class="form-label">Max Devices Per User</label>
                            <input id="max_devices_per_user" type="number" min="1" name="max_devices_per_user"
                                value="{{ old('max_devices_per_user', $generalSetting->max_devices_per_user) }}"
                                class="form-control @error('max_devices_per_user') is-invalid @enderror">
                            @error('max_devices_per_user')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-lg-4">
                            <label for="max_concurrent_sessions" class="form-label">Max Concurrent Sessions</label>
                            <input id="max_concurrent_sessions" type="number" min="1"
                                name="max_concurrent_sessions"
                                value="{{ old('max_concurrent_sessions', $generalSetting->max_concurrent_sessions) }}"
                                class="form-control @error('max_concurrent_sessions') is-invalid @enderror">
                            @error('max_concurrent_sessions')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-lg-6">
                            <label for="cookie_consent_enabled" class="form-label">Cookie Consent Enabled</label>
                            <select id="cookie_consent_enabled" name="cookie_consent_enabled"
                                class="form-select select2 @error('cookie_consent_enabled') is-invalid @enderror">
                                <option value="">Use application default</option>
                                <option value="1" @selected((string) old('cookie_consent_enabled', $generalSetting->cookie_consent_enabled) === '1')>Enabled</option>
                                <option value="0" @selected((string) old('cookie_consent_enabled', $generalSetting->cookie_consent_enabled) === '0')>Disabled</option>
                            </select>
                            @error('cookie_consent_enabled')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-lg-6">
                            <label for="maintenance_mode" class="form-label">Maintenance Mode</label>
                            <select id="maintenance_mode" name="maintenance_mode"
                                class="form-select select2 @error('maintenance_mode') is-invalid @enderror">
                                <option value="">Use application default</option>
                                <option value="1" @selected((string) old('maintenance_mode', $generalSetting->maintenance_mode) === '1')>Enabled</option>
                                <option value="0" @selected((string) old('maintenance_mode', $generalSetting->maintenance_mode) === '0')>Disabled</option>
                            </select>
                            @error('maintenance_mode')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </section>

            <div class="admin-form-actions">
                <button type="submit" class="btn btn-primary admin-primary-button">Update Settings</button>
            </div>
        </form>
    </div>
@endsection
