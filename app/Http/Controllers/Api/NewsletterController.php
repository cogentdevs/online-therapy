<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreNewsletterSubscriptionRequest;
use App\Services\NewsletterSubscriptionService;
use Illuminate\Http\JsonResponse;

class NewsletterController extends Controller
{
    public function store(StoreNewsletterSubscriptionRequest $request, NewsletterSubscriptionService $subscriptions): JsonResponse
    {
        $result = $subscriptions->subscribe($request->string('email')->toString());

        return response()->json([
            'success' => true,
            'message' => $result['already_subscribed']
                ? 'You are already subscribed to the newsletter.'
                : 'Your newsletter subscription has been saved.',
            'already_subscribed' => $result['already_subscribed'],
            'sync_pending' => ! $result['synced'],
        ]);
    }
}
