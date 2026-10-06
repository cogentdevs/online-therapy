<?php

namespace App\Http\Controllers;

use App\Models\Ad;
use App\Models\AdClick;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class AdClickController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Ad $ad): RedirectResponse
    {
        $destination = DB::transaction(function () use ($ad): string {
            $lockedAd = Ad::query()->lockForUpdate()->findOrFail($ad->getKey());

            abort_unless($lockedAd->isCurrentlyTrackable(), 404);

            AdClick::query()->create([
                'ad_id' => $lockedAd->getKey(),
                'clicked_at' => now(),
            ]);

            Ad::query()->whereKey($lockedAd->getKey())->increment('click_count');

            return $lockedAd->ad_url;
        });

        return redirect()->away($destination);
    }
}
