<?php

namespace App\Services;

use App\Models\PaymentAccount;
use App\Models\SubscriptionProduct;
use App\Models\User;
use App\Models\UserSubscription;
use Carbon\CarbonImmutable;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use Throwable;

class SubscriptionPurchaseService
{
    public function __construct(
        private readonly SubscriptionRenewalService $subscriptionRenewalService,
        private readonly ActivityLogService $activityLogService,
    ) {}

    public function purchase(User $user, SubscriptionProduct $selectedProduct, string $paymentMethod): UserSubscription
    {
        return DB::transaction(function () use ($user, $selectedProduct, $paymentMethod): UserSubscription {
            $lockedUser = User::query()->lockForUpdate()->findOrFail($user->getKey());
            $product = SubscriptionProduct::query()
                ->availableToFrontend()
                ->with([
                    'currency:id,code,symbol,isActive',
                    'subscriptionTypes' => fn ($query) => $query
                        ->where('subscription_types.isActive', true)
                        ->select(['subscription_types.id', 'name', 'slug', 'isActive']),
                ])
                ->lockForUpdate()
                ->find($selectedProduct->getKey());

            if (! $product || $product->subscriptionTypes->isEmpty()) {
                throw ValidationException::withMessages([
                    'subscription' => 'یہ سبسکرپشن فی الحال خریداری کے لیے دستیاب نہیں ہے۔',
                ]);
            }

            if (! is_numeric($product->price) || (float) $product->price < 0 || ! $product->currency_id) {
                throw ValidationException::withMessages([
                    'subscription' => 'اس سبسکرپشن کی قیمت یا کرنسی درست نہیں ہے۔',
                ]);
            }

            $purchaseState = $this->subscriptionRenewalService->stateFor($lockedUser, $product);

            if (! in_array($purchaseState['state'], [
                SubscriptionRenewalService::STATE_AVAILABLE,
                SubscriptionRenewalService::STATE_RENEWAL_AVAILABLE,
            ], true)) {
                throw ValidationException::withMessages([
                    'subscription' => $purchaseState['state'] === SubscriptionRenewalService::STATE_RENEWAL_ALREADY_SCHEDULED
                        ? 'اس سبسکرپشن کی تجدید پہلے ہی ہو چکی ہے۔'
                        : 'یہ سبسکرپشن ابھی فعال ہے، تجدید مقررہ مدت میں دستیاب ہوگی۔',
                ]);
            }

            $startDate = $purchaseState['state'] === SubscriptionRenewalService::STATE_RENEWAL_AVAILABLE
                ? CarbonImmutable::instance($purchaseState['current_subscription']->end_date)->addDay()
                : CarbonImmutable::today();
            $endDate = $this->calculateEndDate($startDate, $product->duration_value, $product->duration_unit);
            $effectivePrice = $product->effectivePrice();

            $userSubscription = $lockedUser->userSubscriptions()->create([
                'subscription_product_id' => $product->id,
                'product_for' => $product->product_for,
                'product_name' => $product->getAttribute('name_ur') ?: $product->getAttribute('name_en') ?: $product->name,
                'currency_id' => $product->currency_id,
                'price' => $effectivePrice,
                'discount' => 0,
                'total' => $effectivePrice,
                'payment_method' => $paymentMethod,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'status' => 'active',
                'is_active' => true,
            ]);

            $userSubscription->subscriptionTypes()->attach($product->subscriptionTypes->modelKeys());

            return $userSubscription->load(['user', 'currency', 'subscriptionTypes']);
        });
    }

    public function submitPaymentSlip(
        User $user,
        SubscriptionProduct $selectedProduct,
        int $paymentAccountId,
        UploadedFile $paymentSlip,
        ?string $transactionId = null,
    ): UserSubscription {
        $storedSlipPath = null;

        try {
            return DB::transaction(function () use (
                $user,
                $selectedProduct,
                $paymentAccountId,
                $paymentSlip,
                $transactionId,
                &$storedSlipPath,
            ): UserSubscription {
                $lockedUser = User::query()->lockForUpdate()->findOrFail($user->getKey());
                $product = SubscriptionProduct::query()
                    ->availableToFrontend()
                    ->with([
                        'currency:id,code,symbol,isActive',
                        'subscriptionTypes' => fn ($query) => $query
                            ->where('subscription_types.isActive', true)
                            ->select(['subscription_types.id', 'name', 'slug', 'isActive']),
                    ])
                    ->lockForUpdate()
                    ->find($selectedProduct->getKey());

                if (! $product || $product->subscriptionTypes->isEmpty()) {
                    throw ValidationException::withMessages([
                        'subscription' => 'یہ سبسکرپشن فی الحال خریداری کے لیے دستیاب نہیں ہے۔',
                    ]);
                }

                if (! is_numeric($product->price) || (float) $product->price < 0 || ! $product->currency_id) {
                    throw ValidationException::withMessages([
                        'subscription' => 'اس سبسکرپشن کی قیمت یا کرنسی درست نہیں ہے۔',
                    ]);
                }

                if ($lockedUser->userSubscriptions()
                    ->where('subscription_product_id', $product->id)
                    ->where('status', UserSubscription::STATUS_PENDING)
                    ->where('payment_status', UserSubscription::PAYMENT_STATUS_PENDING)
                    ->exists()) {
                    throw ValidationException::withMessages([
                        'subscription' => 'اس سبسکرپشن کی ادائیگی پہلے ہی جانچ کے لیے زیرِ التوا ہے۔',
                    ]);
                }

                $purchaseState = $this->subscriptionRenewalService->stateFor($lockedUser, $product);

                if (! in_array($purchaseState['state'], [
                    SubscriptionRenewalService::STATE_AVAILABLE,
                    SubscriptionRenewalService::STATE_RENEWAL_AVAILABLE,
                ], true)) {
                    throw ValidationException::withMessages([
                        'subscription' => $purchaseState['state'] === SubscriptionRenewalService::STATE_RENEWAL_ALREADY_SCHEDULED
                            ? 'اس سبسکرپشن کی تجدید پہلے ہی ہو چکی ہے۔'
                            : 'یہ سبسکرپشن ابھی فعال ہے، تجدید مقررہ مدت میں دستیاب ہوگی۔',
                    ]);
                }

                $paymentAccount = PaymentAccount::query()
                    ->where('is_active', true)
                    ->lockForUpdate()
                    ->find($paymentAccountId);

                if ($paymentAccount === null) {
                    throw ValidationException::withMessages([
                        'payment_account_id' => 'منتخب کردہ ادائیگی اکاؤنٹ دستیاب نہیں ہے۔',
                    ]);
                }

                $durationValue = filter_var($product->duration_value, FILTER_VALIDATE_INT);
                $durationUnit = strtolower(trim((string) $product->duration_unit));

                if ($durationValue === false || $durationValue < 1
                    || ! in_array($durationUnit, ['day', 'days', 'week', 'weeks', 'month', 'months', 'year', 'years'], true)) {
                    throw ValidationException::withMessages([
                        'subscription' => 'اس سبسکرپشن کی مدت درست نہیں ہے۔',
                    ]);
                }

                $storedSlipPath = $paymentSlip->store('payment-slips', 'local');

                if (! is_string($storedSlipPath) || $storedSlipPath === '') {
                    throw new RuntimeException('The payment slip could not be stored.');
                }

                $effectivePrice = $product->effectivePrice();
                $userSubscription = $lockedUser->userSubscriptions()->create([
                    'order_no' => null,
                    'invoice_no' => null,
                    'transaction_id' => $transactionId,
                    'subscription_product_id' => $product->id,
                    'product_for' => $product->product_for,
                    'product_name' => $product->getAttribute('name_ur') ?: $product->getAttribute('name_en') ?: $product->name,
                    'currency_id' => $product->currency_id,
                    'price' => $effectivePrice,
                    'discount' => 0,
                    'total' => $effectivePrice,
                    'payment_method' => 'bank_transfer',
                    'payment_account_id' => $paymentAccount->id,
                    'payment_slip' => $storedSlipPath,
                    'payment_status' => UserSubscription::PAYMENT_STATUS_PENDING,
                    'payment_submitted_at' => now(),
                    'reviewed_at' => null,
                    'reviewed_by' => null,
                    'rejection_reason' => null,
                    'duration_value_snapshot' => (int) $durationValue,
                    'duration_unit_snapshot' => $durationUnit,
                    'start_date' => null,
                    'end_date' => null,
                    'status' => UserSubscription::STATUS_PENDING,
                    'is_active' => false,
                ]);

                $userSubscription->subscriptionTypes()->attach($product->subscriptionTypes->modelKeys());

                $submissionAction = $purchaseState['state'] === SubscriptionRenewalService::STATE_RENEWAL_AVAILABLE
                    ? 'renewal_submitted'
                    : 'payment_submitted';
                $this->activityLogService->logUserLifecycle(
                    $lockedUser,
                    $submissionAction,
                    $userSubscription,
                    ($submissionAction === 'renewal_submitted' ? 'Submitted renewal payment' : 'Submitted subscription payment')." #{$userSubscription->id} for review.",
                    null,
                    [
                        'user_id' => $lockedUser->id,
                        'subscription_product_id' => $product->id,
                        'payment_status' => UserSubscription::PAYMENT_STATUS_PENDING,
                        'status' => UserSubscription::STATUS_PENDING,
                        'is_active' => false,
                    ],
                );

                return $userSubscription->load(['user', 'currency', 'subscriptionTypes', 'paymentAccount']);
            });
        } catch (Throwable $exception) {
            if (is_string($storedSlipPath) && $storedSlipPath !== '') {
                Storage::disk('local')->delete($storedSlipPath);
            }

            throw $exception;
        }
    }

    public function approvePayment(UserSubscription $subscription, User $reviewer): UserSubscription
    {
        return DB::transaction(function () use ($subscription, $reviewer): UserSubscription {
            $lockedSubscription = UserSubscription::query()->lockForUpdate()->findOrFail($subscription->getKey());
            $this->ensurePendingReviewState($lockedSubscription);

            if (! $lockedSubscription->payment_account_id
                || ! $lockedSubscription->payment_submitted_at
                || ! $this->hasStoredPaymentSlip($lockedSubscription)) {
                throw ValidationException::withMessages([
                    'subscription' => 'The pending payment request does not contain valid payment evidence.',
                ]);
            }

            $paidPeriods = UserSubscription::query()
                ->where('user_id', $lockedSubscription->user_id)
                ->where('subscription_product_id', $lockedSubscription->subscription_product_id)
                ->whereKeyNot($lockedSubscription->getKey())
                ->where('status', UserSubscription::STATUS_ACTIVE)
                ->where('is_active', true)
                ->whereNotNull('end_date')
                ->whereDate('end_date', '>=', today()->toDateString())
                ->lockForUpdate()
                ->get(['id', 'end_date']);
            $latestPaidEndDate = $paidPeriods->max('end_date');
            $startDate = $latestPaidEndDate !== null
                ? CarbonImmutable::instance($latestPaidEndDate)->addDay()
                : CarbonImmutable::today();
            $endDate = $this->calculateEndDate(
                $startDate,
                $lockedSubscription->duration_value_snapshot,
                $lockedSubscription->duration_unit_snapshot,
            );
            [$orderNumber, $invoiceNumber] = $this->nextDocumentNumbers();
            $oldValues = $this->reviewLogValues($lockedSubscription);

            $lockedSubscription->update([
                'order_no' => $orderNumber,
                'invoice_no' => $invoiceNumber,
                'payment_status' => UserSubscription::PAYMENT_STATUS_APPROVED,
                'status' => UserSubscription::STATUS_ACTIVE,
                'is_active' => true,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'reviewed_by' => $reviewer->id,
                'reviewed_at' => now(),
                'rejection_reason' => null,
            ]);

            $this->activityLogService->log(
                'user_subscriptions',
                'payment_approved',
                $lockedSubscription,
                "Approved subscription payment #{$lockedSubscription->id} for user #{$lockedSubscription->user_id}.",
                $oldValues,
                $this->reviewLogValues($lockedSubscription),
            );

            return $lockedSubscription->refresh();
        });
    }

    public function rejectPayment(UserSubscription $subscription, User $reviewer, string $reason): UserSubscription
    {
        return DB::transaction(function () use ($subscription, $reviewer, $reason): UserSubscription {
            $lockedSubscription = UserSubscription::query()->lockForUpdate()->findOrFail($subscription->getKey());
            $this->ensurePendingReviewState($lockedSubscription);
            $oldValues = $this->reviewLogValues($lockedSubscription);

            $lockedSubscription->update([
                'payment_status' => UserSubscription::PAYMENT_STATUS_REJECTED,
                'status' => UserSubscription::STATUS_REJECTED,
                'is_active' => false,
                'start_date' => null,
                'end_date' => null,
                'reviewed_by' => $reviewer->id,
                'reviewed_at' => now(),
                'rejection_reason' => trim($reason),
            ]);

            $this->activityLogService->log(
                'user_subscriptions',
                'payment_rejected',
                $lockedSubscription,
                "Rejected subscription payment #{$lockedSubscription->id} for user #{$lockedSubscription->user_id}.",
                $oldValues,
                $this->reviewLogValues($lockedSubscription),
            );

            return $lockedSubscription->refresh();
        });
    }

    public function resubmitRejectedPayment(
        User $user,
        UserSubscription $subscription,
        int $paymentAccountId,
        UploadedFile $paymentSlip,
        ?string $transactionId = null,
    ): UserSubscription {
        $newSlipPath = null;
        $oldSlipPath = null;

        try {
            $resubmittedSubscription = DB::transaction(function () use (
                $user,
                $subscription,
                $paymentAccountId,
                $paymentSlip,
                $transactionId,
                &$newSlipPath,
                &$oldSlipPath,
            ): UserSubscription {
                $lockedUser = User::query()->lockForUpdate()->findOrFail($user->getKey());
                $lockedSubscription = $lockedUser->userSubscriptions()
                    ->whereKey($subscription->getKey())
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($lockedSubscription->status !== UserSubscription::STATUS_REJECTED
                    || $lockedSubscription->payment_status !== UserSubscription::PAYMENT_STATUS_REJECTED
                    || $lockedSubscription->is_active) {
                    throw ValidationException::withMessages([
                        'subscription' => 'Only a rejected subscription payment can be resubmitted.',
                    ]);
                }

                if ($lockedUser->userSubscriptions()
                    ->whereKeyNot($lockedSubscription->getKey())
                    ->where('subscription_product_id', $lockedSubscription->subscription_product_id)
                    ->where('status', UserSubscription::STATUS_PENDING)
                    ->where('payment_status', UserSubscription::PAYMENT_STATUS_PENDING)
                    ->exists()) {
                    throw ValidationException::withMessages([
                        'subscription' => 'Your payment request is already pending verification.',
                    ]);
                }

                $paymentAccount = PaymentAccount::query()
                    ->where('is_active', true)
                    ->lockForUpdate()
                    ->find($paymentAccountId);

                if ($paymentAccount === null) {
                    throw ValidationException::withMessages([
                        'payment_account_id' => 'The selected payment account is unavailable.',
                    ]);
                }

                $newSlipPath = $paymentSlip->store('payment-slips', 'local');
                if (! is_string($newSlipPath) || $newSlipPath === '') {
                    throw new RuntimeException('The payment slip could not be stored.');
                }

                $oldSlipPath = $this->safePaymentSlipPath($lockedSubscription->payment_slip);
                $lockedSubscription->update([
                    'order_no' => null,
                    'invoice_no' => null,
                    'transaction_id' => $transactionId,
                    'payment_account_id' => $paymentAccount->id,
                    'payment_slip' => $newSlipPath,
                    'payment_status' => UserSubscription::PAYMENT_STATUS_PENDING,
                    'payment_submitted_at' => now(),
                    'reviewed_at' => null,
                    'reviewed_by' => null,
                    'rejection_reason' => null,
                    'start_date' => null,
                    'end_date' => null,
                    'status' => UserSubscription::STATUS_PENDING,
                    'is_active' => false,
                ]);

                $this->activityLogService->logUserLifecycle(
                    $lockedUser,
                    'payment_resubmitted',
                    $lockedSubscription,
                    "Resubmitted subscription payment #{$lockedSubscription->id} for review.",
                    [
                        'payment_status' => UserSubscription::PAYMENT_STATUS_REJECTED,
                        'status' => UserSubscription::STATUS_REJECTED,
                    ],
                    [
                        'user_id' => $lockedUser->id,
                        'subscription_product_id' => $lockedSubscription->subscription_product_id,
                        'payment_status' => UserSubscription::PAYMENT_STATUS_PENDING,
                        'status' => UserSubscription::STATUS_PENDING,
                        'is_active' => false,
                    ],
                );

                return $lockedSubscription->refresh()->load(['user', 'currency', 'subscriptionTypes', 'paymentAccount']);
            });
        } catch (Throwable $exception) {
            if (is_string($newSlipPath) && $newSlipPath !== '') {
                Storage::disk('local')->delete($newSlipPath);
            }

            throw $exception;
        }

        if ($oldSlipPath !== null && $oldSlipPath !== $newSlipPath) {
            Storage::disk('local')->delete($oldSlipPath);
        }

        return $resubmittedSubscription;
    }

    private function calculateEndDate(CarbonImmutable $startDate, mixed $durationValue, mixed $durationUnit): CarbonImmutable
    {
        $value = filter_var($durationValue, FILTER_VALIDATE_INT);
        $unit = strtolower(trim((string) $durationUnit));

        if ($value === false || $value < 1) {
            throw ValidationException::withMessages([
                'subscription' => 'اس سبسکرپشن کی مدت درست نہیں ہے۔',
            ]);
        }

        return match ($unit) {
            'day', 'days' => $startDate->addDays($value),
            'week', 'weeks' => $startDate->addWeeks($value),
            'month', 'months' => $startDate->addMonthsNoOverflow($value),
            'year', 'years' => $startDate->addYearsNoOverflow($value),
            default => throw ValidationException::withMessages([
                'subscription' => 'اس سبسکرپشن کی مدت کا یونٹ قابل قبول نہیں ہے۔',
            ]),
        };
    }

    private function ensurePendingReviewState(UserSubscription $subscription): void
    {
        if ($subscription->payment_status !== UserSubscription::PAYMENT_STATUS_PENDING
            || $subscription->status !== UserSubscription::STATUS_PENDING
            || $subscription->is_active) {
            throw ValidationException::withMessages([
                'subscription' => 'Only a pending subscription payment can be reviewed.',
            ]);
        }
    }

    private function hasStoredPaymentSlip(UserSubscription $subscription): bool
    {
        $path = str_replace('\\', '/', (string) $subscription->payment_slip);

        return $path !== ''
            && str_starts_with($path, 'payment-slips/')
            && ! str_contains($path, '..')
            && Storage::disk('local')->exists($path);
    }

    private function safePaymentSlipPath(mixed $paymentSlip): ?string
    {
        $path = str_replace('\\', '/', (string) $paymentSlip);

        return $path !== ''
            && str_starts_with($path, 'payment-slips/')
            && ! str_contains($path, '..')
            ? $path
            : null;
    }

    /** @return array{int, int} */
    private function nextDocumentNumbers(): array
    {
        UserSubscription::query()->oldest('id')->lockForUpdate()->firstOrFail();

        $yearPrefix = (int) now()->format('Y') * 10000;
        $lastInvoiceNumber = (int) UserSubscription::query()
            ->whereBetween('invoice_no', [$yearPrefix, $yearPrefix + 9999])
            ->max('invoice_no');
        $lastOrderNumber = (int) UserSubscription::query()
            ->whereBetween('order_no', [$yearPrefix + 1000, $yearPrefix + 9999])
            ->max('order_no');

        return [
            max($yearPrefix + 1000, $lastOrderNumber) + 1,
            max($yearPrefix, $lastInvoiceNumber) + 1,
        ];
    }

    /** @return array<string, mixed> */
    private function reviewLogValues(UserSubscription $subscription): array
    {
        return [
            'user_id' => $subscription->user_id,
            'payment_status' => $subscription->payment_status,
            'status' => $subscription->status,
            'is_active' => $subscription->is_active,
            'start_date' => $subscription->start_date?->toDateString(),
            'end_date' => $subscription->end_date?->toDateString(),
            'reviewed_by' => $subscription->reviewed_by,
            'reviewed_at' => $subscription->reviewed_at?->toDateTimeString(),
        ];
    }
}
