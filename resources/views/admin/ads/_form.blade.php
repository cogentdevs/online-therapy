@php($selectedPage = old('page_name', $ad->page_name ?? ''))
@php($selectedPlace = old('place', $ad->place ?? ''))
<div class="row g-4" data-ad-placement-form data-page-placements='@json($pagePlacements)' @if (! empty($ad?->ad_request_placement_id)) data-ad-linked @endif>
    @if (! isset($ad))
        <div class="col-12"><label class="form-label" for="ad_request_placement_id">Advertising Request (optional)</label>
            <select class="form-select select2 @error('ad_request_placement_id') is-invalid @enderror" id="ad_request_placement_id" name="ad_request_placement_id" data-ad-request-placement>
                <option value="">Standalone advertisement</option>
                @foreach ($requestPlacements as $requestPlacement)
                    <option value="{{ $requestPlacement->id }}" data-page="{{ $requestPlacement->page_name }}" data-place="{{ $requestPlacement->place }}" data-from="{{ $requestPlacement->adRequest->from_date->toDateString() }}" data-to="{{ $requestPlacement->adRequest->to_date->toDateString() }}" @selected((string) old('ad_request_placement_id', request()->query('ad_request_placement_id')) === (string) $requestPlacement->id)>
                        {{ $requestPlacement->adRequest->request_no }} — {{ $requestPlacement->adRequest->company ?: $requestPlacement->adRequest->name }} — {{ $requestPlacement->displayLabel() }}
                    </option>
                @endforeach
            </select>
            <div class="form-text">Selecting a confirmed placement fills and locks its schedule. The server uses the saved request values.</div>
            @error('ad_request_placement_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    @endif
    <div class="col-md-4"><label class="form-label" for="language">Language</label><select
            class="form-select select2 @error('language') is-invalid @enderror" id="language" name="language">
            <option value="">All Languages</option>
            @foreach ($languages as $language)
                <option value="{{ $language->code }}" @selected(old('language', $ad->language ?? '') === $language->code)>{{ $language->name }}</option>
            @endforeach
        </select>
        @error('language')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-8"><label class="form-label" for="title">Title <span
                class="text-danger">*</span></label><input class="form-control @error('title') is-invalid @enderror"
            id="title" name="title" value="{{ old('title', $ad->title ?? '') }}">
        @error('title')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6"><label class="form-label" for="page_name">Page <span
                class="text-danger">*</span></label><select
            class="form-select select2 @error('page_name') is-invalid @enderror" id="page_name" name="page_name"
            data-ad-page>
            <option value="">Select Page</option>
            @foreach ($pagePlacements as $value => $page)
                <option value="{{ $value }}" @selected($selectedPage === $value)>{{ $page['label'] }}</option>
            @endforeach
        </select>
        @error('page_name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6"><label class="form-label" for="place">Place <span
                class="text-danger">*</span></label><select
            class="form-select select2 @error('place') is-invalid @enderror" id="place" name="place" data-ad-place
            data-selected="{{ $selectedPlace }}" @disabled(blank($selectedPage))>
            <option value="">Select Place</option>
        </select>
        @error('place')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6"><label class="form-label" for="ad_image">Ad Image</label>
        @if (isset($ad) && $ad->ad_image)
            <div class="admin-image-preview mb-2"><img src="{{ asset($ad->ad_image) }}" alt="Current ad image"></div>
        @endif
        <input type="file" class="form-control @error('ad_image') is-invalid @enderror" id="ad_image"
            name="ad_image" accept=".png,.jpg,.jpeg,.webp,image/png,image/jpeg,image/webp">
        <div class="form-text">PNG, JPG, JPEG, or WEBP. Maximum 5 MB.</div>
        @error('ad_image')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6"><label class="form-label" for="ad_url">Ad URL</label><input type="url"
            class="form-control @error('ad_url') is-invalid @enderror" id="ad_url" name="ad_url"
            value="{{ old('ad_url', $ad->ad_url ?? '') }}" placeholder="https://example.com">
        @error('ad_url')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-12"><label class="form-label" for="google_ad_code">Google Ad Code</label>
        <textarea class="form-control font-monospace @error('google_ad_code') is-invalid @enderror" id="google_ad_code"
            name="google_ad_code" rows="6">{{ old('google_ad_code', $ad->google_ad_code ?? '') }}</textarea>
        <div class="form-text">Stored as code and never executed in Admin.</div>
        @error('google_ad_code')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-4"><label class="form-label" for="start_date">Start Date</label><input type="date"
            class="form-control @error('start_date') is-invalid @enderror" id="start_date" name="start_date"
            value="{{ old('start_date', isset($ad) && $ad->start_date ? $ad->start_date->format('Y-m-d') : '') }}">
        @error('start_date')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-4"><label class="form-label" for="expiry_date">Expiry Date</label><input type="date"
            class="form-control @error('expiry_date') is-invalid @enderror" id="expiry_date" name="expiry_date"
            value="{{ old('expiry_date', isset($ad) && $ad->expiry_date ? $ad->expiry_date->format('Y-m-d') : '') }}">
        @error('expiry_date')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-4"><label class="form-label d-block">Active</label><input type="hidden" name="isActive"
            value="0">
        <div class="form-check form-switch"><input class="form-check-input" type="checkbox" role="switch"
                id="isActive" name="isActive" value="1" @checked((bool) old('isActive', $ad->isActive ?? true))><label
                class="form-check-label" for="isActive">Enabled</label></div>
    </div>
</div>
