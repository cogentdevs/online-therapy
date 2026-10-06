<?php

namespace App\Services;

use App\Models\Ad;
use App\Models\AdClick;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AdApiService
{
    /** @return Collection<int, Ad> */
    public function eligibleAds(string $page, ?string $placement): Collection
    {
        return Ad::query()
            ->currentlyEligible()
            ->where('page_name', $page)
            ->when($placement, fn ($query, string $value) => $query->where('place', $value))
            ->where(function ($query): void {
                $query->where('language', config('content_language.code'))->orWhereNull('language');
            })
            ->whereNotNull('ad_image')
            ->where('ad_image', '!=', '')
            ->orderByRaw('CASE WHEN language = ? THEN 0 ELSE 1 END', [config('content_language.code')])
            ->orderByDesc('id')
            ->get(['id', 'language', 'title', 'page_name', 'place', 'ad_image', 'ad_url', 'google_ad_code', 'start_date', 'expiry_date', 'isActive'])
            ->unique('place')
            ->values();
    }

    /** @return array{tracked: true, target_url: string} */
    public function trackClick(Ad $ad): array
    {
        return DB::transaction(function () use ($ad): array {
            $lockedAd = Ad::query()->lockForUpdate()->findOrFail($ad->getKey());

            abort_unless($lockedAd->isCurrentlyTrackable(), 404);

            AdClick::query()->create(['ad_id' => $lockedAd->getKey(), 'clicked_at' => now()]);
            Ad::query()->whereKey($lockedAd->getKey())->increment('click_count');

            return ['tracked' => true, 'target_url' => $lockedAd->ad_url];
        });
    }
}
