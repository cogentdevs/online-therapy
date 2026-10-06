<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\SubscriptionInvoiceService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SubscriptionInvoiceController extends Controller
{
    public function __construct(
        private readonly SubscriptionInvoiceService $subscriptionInvoiceService,
    ) {}

    public function view(Request $request, int $subscription): Response
    {
        return $this->render($request, $subscription, false);
    }

    public function download(Request $request, int $subscription): Response
    {
        return $this->render($request, $subscription, true);
    }

    private function render(Request $request, int $subscriptionId, bool $download): Response
    {
        $userSubscription = $request->user('web')->userSubscriptions()
            ->with(['user', 'currency', 'subscriptionTypes', 'subscriptionProduct'])
            ->findOrFail($subscriptionId);
        abort_unless($userSubscription->hasFinalInvoice(), 404);

        $filename = $this->subscriptionInvoiceService->filename($userSubscription);
        $pdf = $this->subscriptionInvoiceService->pdf($userSubscription);

        return $download ? $pdf->download($filename) : $pdf->stream($filename);
    }
}
