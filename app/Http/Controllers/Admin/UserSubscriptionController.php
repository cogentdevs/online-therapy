<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RejectSubscriptionPaymentRequest;
use App\Models\User;
use App\Models\UserSubscription;
use App\Services\SubscriptionPaymentMailService;
use App\Services\SubscriptionPresentationService;
use App\Services\SubscriptionPurchaseService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UserSubscriptionController extends Controller
{
    public function __construct(
        private readonly SubscriptionPresentationService $subscriptionPresentationService,
        private readonly SubscriptionPurchaseService $subscriptionPurchaseService,
        private readonly SubscriptionPaymentMailService $subscriptionPaymentMailService,
    ) {}

    public function index(): View
    {
        Gate::authorize('user-subscriptions.view');

        $subscriptions = UserSubscription::query()
            ->with([
                'user:id,name,email',
                'currency:id,name,code,symbol',
                'subscriptionTypes:id,name,slug',
                'paymentAccount:id,bank_name,account_title,account_no,iban,branch_code',
            ])
            ->latest()
            ->get();

        $this->subscriptionPresentationService->prepare($subscriptions);

        return view('admin.user-subscriptions.index', compact('subscriptions'));
    }

    public function show(UserSubscription $userSubscription): View
    {
        Gate::authorize('user-subscriptions.view');

        $userSubscription->load([
            'user:id,name,email,phone',
            'currency:id,name,code,symbol',
            'subscriptionTypes:id,name,slug',
            'paymentAccount:id,bank_name,account_title,account_no,iban,branch_code',
            'reviewer:id,name,email',
        ]);
        $this->subscriptionPresentationService->prepare([$userSubscription]);

        return view('admin.user-subscriptions.show', compact('userSubscription'));
    }

    public function viewSlip(UserSubscription $userSubscription): StreamedResponse
    {
        Gate::authorize('user-subscriptions.view');

        return $this->paymentSlipResponse($userSubscription, inline: true);
    }

    public function downloadSlip(UserSubscription $userSubscription): StreamedResponse
    {
        Gate::authorize('user-subscriptions.view');

        return $this->paymentSlipResponse($userSubscription, inline: false);
    }

    public function approve(Request $request, UserSubscription $userSubscription): RedirectResponse
    {
        Gate::authorize('user-subscriptions.approve');
        $reviewer = $request->user();
        abort_unless($reviewer instanceof User, 403);

        $approvedSubscription = $this->subscriptionPurchaseService->approvePayment($userSubscription, $reviewer);
        $this->subscriptionPaymentMailService->sendApproved($approvedSubscription);

        return to_route('admin.user-subscriptions.show', $userSubscription)
            ->with('status', 'Subscription payment approved successfully.');
    }

    public function reject(
        RejectSubscriptionPaymentRequest $request,
        UserSubscription $userSubscription,
    ): RedirectResponse {
        $reviewer = $request->user();
        abort_unless($reviewer instanceof User, 403);

        $rejectedSubscription = $this->subscriptionPurchaseService->rejectPayment(
            $userSubscription,
            $reviewer,
            $request->validated('rejection_reason'),
        );
        $this->subscriptionPaymentMailService->sendRejected($rejectedSubscription);

        return to_route('admin.user-subscriptions.show', $userSubscription)
            ->with('status', 'Subscription payment rejected successfully.');
    }

    private function paymentSlipResponse(UserSubscription $subscription, bool $inline): StreamedResponse
    {
        $path = str_replace('\\', '/', (string) $subscription->payment_slip);
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        abort_unless(
            $path !== ''
            && str_starts_with($path, 'payment-slips/')
            && ! str_contains($path, '..')
            && in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'pdf'], true)
            && Storage::disk('local')->exists($path),
            404,
        );

        $mimeType = Storage::disk('local')->mimeType($path);
        abort_unless(in_array($mimeType, [
            'image/jpeg',
            'image/png',
            'image/webp',
            'application/pdf',
        ], true), 404);

        $stream = Storage::disk('local')->readStream($path);
        abort_unless(is_resource($stream), 404);
        $fallbackName = $extension === 'pdf' ? 'payment-slip.pdf' : 'payment-slip.'.$extension;
        $fileName = 'payment-slip-'.$subscription->id.'.'.$extension;
        $disposition = HeaderUtils::makeDisposition(
            $inline ? HeaderUtils::DISPOSITION_INLINE : HeaderUtils::DISPOSITION_ATTACHMENT,
            $fileName,
            $fallbackName,
        );

        return response()->stream(function () use ($stream): void {
            try {
                fpassthru($stream);
            } finally {
                fclose($stream);
            }
        }, 200, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => $disposition,
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-store',
        ]);
    }
}
