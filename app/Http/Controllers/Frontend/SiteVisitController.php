<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\UpdateSiteVisitEngagementRequest;
use App\Models\SiteVisit;
use App\Services\SiteVisitService;
use Illuminate\Http\JsonResponse;

class SiteVisitController extends Controller
{
    public function update(UpdateSiteVisitEngagementRequest $request): JsonResponse
    {
        $visit = SiteVisit::query()
            ->where('public_token_hash', hash('sha256', $request->string('token')->toString()))
            ->first();

        if ($visit === null || $visit->visitor_id !== $request->cookie(SiteVisitService::VISITOR_COOKIE)) {
            abort(404);
        }

        $userId = $request->user('web')?->getKey();
        abort_unless($visit->user_id === null ? $userId === null : $visit->user_id === $userId, 404);

        $now = now();
        $visit->increment('duration_seconds', $request->integer('active_seconds'), [
            'last_activity_at' => $now,
            'ended_at' => $request->boolean('ended') ? $now : null,
        ]);

        return response()->json(['success' => true]);
    }
}
