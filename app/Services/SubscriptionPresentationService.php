<?php

namespace App\Services;

use App\Models\SubscriptionType;
use App\Models\UserSubscription;
use Illuminate\Support\Str;

class SubscriptionPresentationService
{
    /** @param iterable<int, UserSubscription> $subscriptions */
    public function prepare(iterable $subscriptions): void
    {
        foreach ($subscriptions as $subscription) {
            $status = $this->effectiveStatus($subscription);

            $subscription->setAttribute('frontend_product_type', $this->productTypeLabel($subscription->product_for));
            $subscription->setAttribute('frontend_effective_status', $status);
            $subscription->setAttribute('frontend_status_label', $this->statusLabel($status));
            $subscription->setAttribute('frontend_payment_status', $this->paymentStatus($subscription));
            $subscription->setAttribute('frontend_payment_status_label', $this->paymentStatusLabel($subscription));
            $subscription->setAttribute('frontend_payment_method', $this->paymentMethodLabel($subscription->payment_method));
            $subscription->setAttribute('frontend_amount', $this->formatAmount($subscription->total ?? $subscription->price));

            $subscription->subscriptionTypes->each(function (SubscriptionType $type): void {
                $type->setAttribute('frontend_label', $this->moduleLabel($type));
                $type->setAttribute('frontend_icon', $this->moduleIcon($type));
            });
        }
    }

    public function effectiveStatus(UserSubscription $subscription): string
    {
        $today = today();

        if ($subscription->is_active
            && $subscription->status === 'active'
            && ($subscription->start_date === null || ! $subscription->start_date->isAfter($today))
            && ($subscription->end_date === null || ! $subscription->end_date->isBefore($today))) {
            return 'active';
        }

        if ($subscription->end_date?->isBefore($today)) {
            return 'expired';
        }

        if ($subscription->start_date?->isAfter($today)) {
            return 'future';
        }

        $storedStatus = Str::lower(trim((string) $subscription->status));

        return ! $subscription->is_active && in_array($storedStatus, ['', 'active'], true)
            ? 'inactive'
            : ($storedStatus ?: 'inactive');
    }

    private function productTypeLabel(?string $productFor): string
    {
        return match (Str::lower(trim((string) $productFor))) {
            'plan' => 'منصوبہ',
            'membership' => 'رکنیت',
            default => $productFor ?: 'سبسکرپشن',
        };
    }

    private function statusLabel(string $status): string
    {
        return match ($status) {
            'active' => 'فعال',
            'expired' => 'میعاد ختم',
            'future' => 'طے شدہ',
            'pending' => 'ادائیگی تصدیق کے لیے زیرِ التوا',
            'rejected' => 'ادائیگی مسترد',
            'inactive' => 'غیر فعال',
            'cancelled', 'canceled' => 'منسوخ',
            default => $status,
        };
    }

    private function paymentStatus(UserSubscription $subscription): string
    {
        return match ($subscription->payment_status) {
            UserSubscription::PAYMENT_STATUS_PENDING => 'pending',
            UserSubscription::PAYMENT_STATUS_APPROVED => 'approved',
            UserSubscription::PAYMENT_STATUS_REJECTED => 'rejected',
            default => 'legacy',
        };
    }

    private function paymentStatusLabel(UserSubscription $subscription): string
    {
        return match ($this->paymentStatus($subscription)) {
            'pending' => 'تصدیق زیرِ التوا',
            'approved' => 'منظور شدہ',
            'rejected' => 'مسترد',
            default => 'سابقہ ادائیگی',
        };
    }

    private function paymentMethodLabel(?string $paymentMethod): string
    {
        return match (Str::lower(trim((string) $paymentMethod))) {
            'easypaisa' => 'Easypaisa',
            'jazzcash' => 'JazzCash',
            'card' => 'Master / Visa Card',
            'bank_transfer' => 'بینک ٹرانسفر',
            default => $paymentMethod ?: 'درج نہیں',
        };
    }

    private function moduleLabel(SubscriptionType $type): string
    {
        return match (Str::lower(trim((string) $type->name))) {
            'magazine', 'magazines' => 'ہفتہ وار میگزین',
            'article', 'articles' => 'مضامین',
            'audio' => 'آڈیو',
            default => $type->name,
        };
    }

    private function moduleIcon(SubscriptionType $type): string
    {
        return match (Str::lower(trim((string) $type->name))) {
            'magazine', 'magazines' => 'fa-solid fa-book-open',
            'article', 'articles' => 'fa-regular fa-file-lines',
            'audio' => 'fa-solid fa-headphones',
            default => 'fa-solid fa-shapes',
        };
    }

    private function formatAmount(mixed $amount): string
    {
        if (! is_numeric($amount)) {
            return '—';
        }

        $numericAmount = (float) $amount;
        $decimals = floor($numericAmount) === $numericAmount ? 0 : 2;

        return number_format($numericAmount, $decimals);
    }
}
