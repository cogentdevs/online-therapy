<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\SearchClickRequest;
use App\Services\SearchAnalyticsService;
use Illuminate\Http\JsonResponse;

class AnalyticsController extends Controller
{
    public function __construct(private readonly SearchAnalyticsService $searchAnalyticsService) {}

    public function searchClick(SearchClickRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $this->searchAnalyticsService->track(
            $validated['type'],
            (int) $validated['content_id'],
            $validated['search_keyword'],
        );

        return response()->json(['success' => true, 'data' => ['tracked' => true]]);
    }
}
