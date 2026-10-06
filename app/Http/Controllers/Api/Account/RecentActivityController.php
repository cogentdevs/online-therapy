<?php

namespace App\Http\Controllers\Api\Account;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Account\AccountListRequest;
use App\Services\Api\MobileAccountReadService;
use Illuminate\Http\JsonResponse;

class RecentActivityController extends Controller
{
    public function __construct(private readonly MobileAccountReadService $accountReadService) {}

    public function index(AccountListRequest $request): JsonResponse
    {
        $activities = $this->accountReadService->recentActivities($request->user(), $request->validated());

        return response()->json(['success' => true, 'data' => [
            'recent_activities' => collect($activities->items())->map(fn ($activity): array => $this->accountReadService->recentActivity($activity))->all(),
            'pagination' => $this->accountReadService->pagination($activities),
        ]]);
    }
}
