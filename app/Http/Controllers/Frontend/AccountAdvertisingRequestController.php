<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Ad;
use App\Models\AdRequest;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccountAdvertisingRequestController extends Controller
{
    private const REQUEST_STATUS_LABELS = [
        'pending' => 'زیرِ جائزہ',
        'quote_sent' => 'کوٹیشن ارسال کر دی گئی',
        'confirmed' => 'تصدیق شدہ',
        'published' => 'شائع شدہ',
        'rejected' => 'مسترد',
        'cancelled' => 'منسوخ',
    ];

    private const PLACEMENT_STATUS_LABELS = [
        'pending' => 'زیرِ جائزہ',
        'quoted' => 'کوٹیشن ارسال شدہ',
        'confirmed' => 'تصدیق شدہ',
        'conflicted' => 'منتخب مدت میں دستیاب نہیں',
        'published' => 'شائع شدہ',
        'cancelled' => 'منسوخ',
    ];

    public function index(Request $request): View
    {
        $adRequests = AdRequest::query()
            ->where('user_id', $request->user('web')->getKey())
            ->withCount('placements')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(10);

        return view('frontend.user-account.advertising-requests', [
            'adRequests' => $adRequests,
            'requestStatusLabels' => self::REQUEST_STATUS_LABELS,
        ]);
    }

    public function show(Request $request, AdRequest $adRequest): View
    {
        abort_unless($adRequest->user_id === $request->user('web')->getKey(), 404);

        $adRequest->load(['placements.ad']);
        $adDetails = [];

        foreach ($adRequest->placements as $placement) {
            if ($placement->ad === null) {
                continue;
            }

            $adDetails[$placement->getKey()] = [
                'status' => $this->adStatus($placement->ad),
                'click_count_available' => $this->clickCountAvailable($placement->ad),
            ];
        }

        return view('frontend.user-account.advertising-request-detail', [
            'adRequest' => $adRequest,
            'adDetails' => $adDetails,
            'requestStatusLabels' => self::REQUEST_STATUS_LABELS,
            'placementStatusLabels' => self::PLACEMENT_STATUS_LABELS,
        ]);
    }

    private function adStatus(Ad $ad): string
    {
        if (! $ad->isActive) {
            return 'غیر فعال';
        }

        if ($ad->expiry_date?->isBefore(today())) {
            return 'مدت ختم ہو گئی';
        }

        if ($ad->start_date?->isAfter(today())) {
            return 'آئندہ شائع ہوگا';
        }

        return 'فعال';
    }

    private function clickCountAvailable(Ad $ad): bool
    {
        if (blank($ad->ad_image) || filled($ad->google_ad_code) || ! is_string($ad->ad_url)) {
            return false;
        }

        $scheme = strtolower((string) parse_url($ad->ad_url, PHP_URL_SCHEME));

        return filter_var($ad->ad_url, FILTER_VALIDATE_URL) !== false
            && in_array($scheme, ['http', 'https'], true);
    }
}
