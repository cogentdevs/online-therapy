<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AdIndexRequest;
use App\Models\Ad;
use App\Services\AdApiService;
use Illuminate\Http\JsonResponse;

class AdController extends Controller
{
    public function __construct(private readonly AdApiService $adApiService) {}

    public function index(AdIndexRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $ads = $this->adApiService->eligibleAds($validated['page'], $validated['placement'] ?? null);

        return response()->json([
            'success' => true,
            'data' => ['ads' => $ads->map(fn (Ad $ad): array => [
                'id' => $ad->id,
                'page' => $ad->page_name,
                'placement' => $ad->place,
                'title' => $ad->title,
                'type' => 'image',
                'image_url' => $this->assetUrl($ad->ad_image),
                'target_url' => $ad->isCurrentlyTrackable() ? $ad->ad_url : null,
            ])->values()],
        ]);
    }

    public function click(Ad $ad): JsonResponse
    {
        return response()->json(['success' => true, 'data' => $this->adApiService->trackClick($ad)]);
    }

    private function assetUrl(?string $path): ?string
    {
        return filled($path) ? asset(ltrim(str_replace('\\', '/', $path), '/')) : null;
    }
}
