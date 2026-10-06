<?php

namespace App\Http\Controllers\Api\Account;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Account\AccountListRequest;
use App\Http\Requests\Api\PurchaseSubscriptionRequest;
use App\Models\SubscriptionProduct;
use App\Services\Api\MobileAccountReadService;
use App\Services\Api\SubscriptionPurchaseMailService;
use App\Services\SubscriptionInvoiceService;
use App\Services\SubscriptionPurchaseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SubscriptionController extends Controller
{
    public function __construct(
        private readonly MobileAccountReadService $accountReadService,
        private readonly SubscriptionInvoiceService $invoiceService,
        private readonly SubscriptionPurchaseService $purchaseService,
        private readonly SubscriptionPurchaseMailService $purchaseMailService,
    ) {}

    public function purchase(PurchaseSubscriptionRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $product = SubscriptionProduct::query()->findOrFail($validated['subscription_product_id']);
        $subscription = $this->purchaseService->purchase(
            $request->user(),
            $product,
            $validated['payment_method'],
        );

        $this->purchaseMailService->send($subscription);

        return response()->json([
            'success' => true,
            'message' => 'Subscription purchased successfully.',
            'data' => [
                'subscription' => $this->accountReadService->subscription($subscription),
            ],
        ], 201);
    }

    public function active(Request $request): JsonResponse
    {
        return response()->json(['success' => true, 'data' => $this->accountReadService->activeSubscriptions($request->user())]);
    }

    public function entitlements(Request $request): JsonResponse
    {
        return response()->json(['success' => true, 'data' => $this->accountReadService->entitlements($request->user())]);
    }

    public function index(AccountListRequest $request): JsonResponse
    {
        $subscriptions = $this->accountReadService->subscriptions($request->user(), $request->validated());

        return response()->json(['success' => true, 'data' => [
            'subscriptions' => collect($subscriptions->items())->map(fn ($subscription): array => $this->accountReadService->subscription($subscription))->all(),
            'pagination' => $this->accountReadService->pagination($subscriptions),
        ]]);
    }

    public function invoice(Request $request, int $subscription): Response
    {
        return $this->renderInvoice($request, $subscription, false);
    }

    public function downloadInvoice(Request $request, int $subscription): Response
    {
        return $this->renderInvoice($request, $subscription, true);
    }

    private function renderInvoice(Request $request, int $subscription, bool $download): Response
    {
        $record = $request->user()->userSubscriptions()
            ->with(['user', 'currency', 'subscriptionTypes', 'subscriptionProduct'])->findOrFail($subscription);
        abort_unless($record->hasFinalInvoice(), 404);
        $filename = $this->invoiceService->filename($record);
        $pdf = $this->invoiceService->pdf($record);

        return $download ? $pdf->download($filename) : $pdf->stream($filename);
    }
}
