<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAdRequest;
use App\Http\Requests\Admin\UpdateAdRequest;
use App\Models\Ad;
use App\Models\AdRequestPlacement;
use App\Models\Language;
use App\Services\AdPlacementAvailabilityService;
use App\Services\AdRequestWorkflowService;
use App\Services\AdvertisingRequestMailService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;

class AdsController extends Controller
{
    private const IMAGE_DIRECTORY = 'images/backend-images/ads';

    public function index(): View
    {
        return view('admin.ads.view-ad', ['ads' => Ad::query()->with('creator.roles')->latest()->get(), 'pagePlacements' => Ad::PAGE_PLACEMENTS]);
    }

    public function create(): View
    {
        return view('admin.ads.add-ad', $this->formData());
    }

    public function store(StoreAdRequest $request, AdPlacementAvailabilityService $availability, AdRequestWorkflowService $workflow, AdvertisingRequestMailService $mail): RedirectResponse
    {
        $data = $request->validated();
        $image = null;
        $publishedPlacement = null;
        try {
            if ($request->hasFile('ad_image')) {
                $image = $this->storeImage($request->file('ad_image'));
            }

            DB::transaction(function () use ($data, $image, $availability, $workflow, &$publishedPlacement): void {
                $attributes = [...Arr::except($data, ['ad_image', 'ad_request_placement_id']), 'ad_image' => $image, 'isActive' => $data['isActive'] ?? false];
                if (filled($data['ad_request_placement_id'] ?? null)) {
                    $placement = AdRequestPlacement::query()->with('adRequest')->findOrFail($data['ad_request_placement_id']);
                    $adRequest = $placement->adRequest;
                    $workflow->lockOverlappingRequests($adRequest);
                    $placement = AdRequestPlacement::query()->with('adRequest')->lockForUpdate()->findOrFail($placement->id);
                    $adRequest = $placement->adRequest;
                    if ($placement->status !== 'confirmed' || $placement->ad()->exists() || $adRequest->status !== 'confirmed' || $adRequest->to_date->isBefore(today())) {
                        throw ValidationException::withMessages(['ad_request_placement_id' => 'This request placement is no longer eligible.']);
                    }
                    if (! $availability->isAvailable($placement->page_name, $placement->place, $adRequest->from_date, $adRequest->to_date, excludePlacementId: $placement->id)) {
                        throw ValidationException::withMessages(['ad_request_placement_id' => 'This placement now conflicts with another booking.']);
                    }
                    $attributes = [...$attributes,
                        'page_name' => $placement->page_name,
                        'place' => $placement->place,
                        'start_date' => $adRequest->from_date,
                        'expiry_date' => $adRequest->to_date,
                        'ad_request_id' => $adRequest->id,
                        'ad_request_placement_id' => $placement->id,
                    ];
                }

                Ad::query()->create($attributes);
                if (isset($placement)) {
                    $placement->status = 'published';
                    $placement->save();
                    $workflow->recalculate($adRequest);
                    $publishedPlacement = $placement;
                }
            });
        } catch (Throwable $exception) {
            $this->deleteImage($image);
            throw $exception;
        }

        if ($publishedPlacement !== null) {
            $mail->sendStatusUpdate($publishedPlacement->adRequest, 'published', $publishedPlacement);
        }

        return redirect()->route('admin.ads.index')->with('status', 'Advertisement added successfully.');
    }

    public function show(int $id): View
    {
        return view('admin.ads.view-ad-detail', ['ad' => Ad::query()->with('creator.roles')->findOrFail($id), 'pagePlacements' => Ad::PAGE_PLACEMENTS]);
    }

    public function edit(int $id): View
    {
        return view('admin.ads.edit-ad', [...$this->formData(), 'ad' => Ad::query()->findOrFail($id)]);
    }

    public function update(UpdateAdRequest $request, int $id): RedirectResponse
    {
        $ad = Ad::query()->findOrFail($id);
        $data = Arr::except($request->validated(), ['ad_image', 'ad_request_placement_id']);
        $data['isActive'] = $request->boolean('isActive');
        if ($ad->ad_request_placement_id !== null) {
            $placement = $ad->adRequestPlacement()->with('adRequest')->firstOrFail();
            $data['page_name'] = $placement->page_name;
            $data['place'] = $placement->place;
            $data['start_date'] = $placement->adRequest->from_date;
            $data['expiry_date'] = $placement->adRequest->to_date;
        }
        $new = null;
        $old = null;
        try {
            if ($request->hasFile('ad_image')) {
                $new = $this->storeImage($request->file('ad_image'));
                $data['ad_image'] = $new;
                $old = $ad->ad_image;
            } DB::transaction(fn () => $ad->update($data));
        } catch (Throwable $exception) {
            $this->deleteImage($new);
            throw $exception;
        }
        if ($old !== null && $old !== $new) {
            $this->deleteImage($old);
        }

        return redirect()->route('admin.ads.index')->with('status', 'Advertisement updated successfully.');
    }

    public function destroy(int $id, AdRequestWorkflowService $workflow): RedirectResponse
    {
        $ad = Ad::query()->findOrFail($id);
        $image = $ad->ad_image;
        DB::transaction(function () use ($ad, $workflow): void {
            $source = $ad->adRequestPlacement()->with('adRequest')->first();
            if ($source !== null) {
                $workflow->lockOverlappingRequests($source->adRequest);
            }
            $placement = $ad->adRequestPlacement()->with('adRequest')->lockForUpdate()->first();
            $ad->delete();
            if ($placement !== null) {
                $placement->status = 'confirmed';
                $placement->save();
                $workflow->recalculate($placement->adRequest);
            }
        });
        $this->deleteImage($image);

        return redirect()->route('admin.ads.index')->with('status', 'Advertisement deleted successfully.');
    }

    private function formData(): array
    {
        return [
            'languages' => Language::query()->where('is_active', true)->orderBy('name')->get(['name', 'code']),
            'pagePlacements' => Ad::PAGE_PLACEMENTS,
            'requestPlacements' => AdRequestPlacement::query()->with('adRequest')
                ->where('status', 'confirmed')->whereDoesntHave('ad')
                ->whereHas('adRequest', fn ($query) => $query->where('status', 'confirmed')->whereDate('to_date', '>=', today()))
                ->latest()->get(),
        ];
    }

    private function storeImage(UploadedFile $image): string
    {
        $directory = public_path(self::IMAGE_DIRECTORY);
        File::ensureDirectoryExists($directory);
        $base = Str::of(pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME))->ascii()->replaceMatches('/[^A-Za-z0-9]+/', '-')->trim('-')->toString() ?: 'ad';
        $filename = $base.'.'.strtolower($image->extension());
        for ($suffix = 2; File::exists($directory.DIRECTORY_SEPARATOR.$filename); $suffix++) {
            $filename = $base.'-'.$suffix.'.'.strtolower($image->extension());
        }
        $image->move($directory, $filename);

        return self::IMAGE_DIRECTORY.'/'.$filename;
    }

    private function deleteImage(?string $path): void
    {
        $path = str_replace('\\', '/', (string) $path);
        if ($path && $path === self::IMAGE_DIRECTORY.'/'.basename($path)) {
            File::delete(public_path($path));
        }
    }
}
