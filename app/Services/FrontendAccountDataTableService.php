<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Magazine;
use App\Models\SubscriptionProduct;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class FrontendAccountDataTableService
{
    public function __construct(
        private readonly BookmarkPresentationService $bookmarkPresentationService,
        private readonly SubscriptionPresentationService $subscriptionPresentationService,
        private readonly UserContentVisitPresentationService $contentVisitPresentationService,
    ) {}

    /** @return array<string, mixed> */
    public function bookmarks(User $user, Request $request): array
    {
        $query = $user->bookmarks()->getQuery()->with('bookmarkable');
        $total = (clone $query)->count();
        $search = $this->search($request);

        if ($search !== '') {
            $query->whereHasMorph(
                'bookmarkable',
                [Article::class, Magazine::class],
                fn (Builder $query): Builder => $query->where('title', 'like', '%'.$search.'%'),
            );
        }

        $filtered = (clone $query)->count();
        $this->orderPolymorphicContentQuery($query, $request, 'bookmarkable', [
            0 => 'type', 1 => 'title', 2 => 'created_at',
        ], 'created_at');
        $bookmarks = $query->offset($this->start($request))->limit($this->length($request))->get();
        $this->bookmarkPresentationService->prepare($bookmarks);

        return $this->response($request, $total, $filtered, $bookmarks->map(fn ($bookmark): array => [
            'module' => $bookmark->frontend_module_label,
            'title' => $bookmark->frontend_title,
            'bookmarked_at' => $bookmark->created_at?->format('d M Y') ?? '—',
            'url' => $bookmark->frontend_url,
            'destroy_url' => route('front.account.bookmarks.destroy', $bookmark),
        ])->all());
    }

    /** @return array<string, mixed> */
    public function recentActivities(User $user, Request $request): array
    {
        $query = $user->contentVisits()->getQuery()->with('visitable');
        $total = (clone $query)->count();
        $search = $this->search($request);

        if ($search !== '') {
            $query->whereHasMorph(
                'visitable',
                [Article::class, Magazine::class],
                fn (Builder $query): Builder => $query->where('title', 'like', '%'.$search.'%'),
            );
        }

        $filtered = (clone $query)->count();
        $this->orderPolymorphicContentQuery($query, $request, 'visitable', [
            0 => 'type', 1 => 'title', 2 => 'last_visited_at',
        ], 'last_visited_at');
        $activities = $query->offset($this->start($request))->limit($this->length($request))->get();
        $this->contentVisitPresentationService->prepare($activities);

        return $this->response($request, $total, $filtered, $activities->map(fn ($activity): array => [
            'module' => $activity->frontend_module_label,
            'title' => $activity->frontend_title,
            'last_visited_at' => $activity->frontend_last_visited_at ?? '—',
            'url' => $activity->frontend_url,
        ])->all());
    }

    /** @return array<string, mixed> */
    public function subscriptions(User $user, Request $request): array
    {
        $query = $user->userSubscriptions()->getQuery()->with(['subscriptionTypes', 'currency']);
        $total = (clone $query)->count();
        $search = $this->search($request);

        if ($search !== '') {
            $query->where(function (Builder $query) use ($search): void {
                $query->where('product_name', 'like', '%'.$search.'%')
                    ->orWhere('product_for', 'like', '%'.$search.'%')
                    ->orWhere('payment_method', 'like', '%'.$search.'%')
                    ->orWhere('status', 'like', '%'.$search.'%')
                    ->orWhereHas('subscriptionTypes', fn (Builder $query): Builder => $query->where('name', 'like', '%'.$search.'%'));
            });
        }

        $filtered = (clone $query)->count();
        $columns = [0 => 'product_name', 2 => 'total', 3 => 'created_at', 4 => 'payment_method', 5 => 'status'];
        $column = $columns[$this->orderColumn($request)] ?? 'created_at';
        $query->orderBy($column, $this->orderDirection($request))->orderByDesc('id');
        $subscriptions = $query->offset($this->start($request))->limit($this->length($request))->get();
        $this->subscriptionPresentationService->prepare($subscriptions);

        return $this->response($request, $total, $filtered, $subscriptions->map(fn ($subscription): array => [
            'product_name' => $subscription->product_name ?: 'سبسکرپشن',
            'product_type' => $subscription->frontend_product_type,
            'modules' => $subscription->subscriptionTypes->map(fn ($type): array => [
                'label' => $type->frontend_label,
                'icon' => $type->frontend_icon,
            ])->all(),
            'currency' => $subscription->currency?->code ?? $subscription->currency?->symbol,
            'amount' => $subscription->frontend_amount,
            'purchased_at' => $subscription->created_at?->format('d M Y') ?? '—',
            'payment_submitted_at' => $subscription->payment_submitted_at?->format('d M Y, h:i A') ?? '—',
            'start_date' => $subscription->start_date?->format('d M Y') ?? '—',
            'end_date' => $subscription->end_date?->format('d M Y') ?? 'کوئی اختتامی تاریخ نہیں',
            'reviewed_at' => $subscription->reviewed_at?->format('d M Y, h:i A') ?? '—',
            'payment_method' => $subscription->frontend_payment_method,
            'payment_status' => $subscription->frontend_payment_status,
            'payment_status_label' => $subscription->frontend_payment_status_label,
            'transaction_id' => $subscription->transaction_id,
            'status' => $subscription->frontend_effective_status,
            'status_label' => $subscription->frontend_status_label,
            'invoice_no' => $subscription->hasFinalInvoice() ? $subscription->invoice_no : null,
            'order_no' => $subscription->hasFinalInvoice() ? $subscription->order_no : null,
            'invoice_view_url' => $subscription->hasFinalInvoice()
                ? route('front.account.subscriptions.invoice.view', $subscription)
                : null,
            'invoice_download_url' => $subscription->hasFinalInvoice()
                ? route('front.account.subscriptions.invoice.download', $subscription)
                : null,
            'rejection_reason' => $subscription->payment_status === UserSubscription::PAYMENT_STATUS_REJECTED
                ? $subscription->rejection_reason
                : null,
            'resubmit_url' => $subscription->payment_status === UserSubscription::PAYMENT_STATUS_REJECTED
                && $subscription->status === UserSubscription::STATUS_REJECTED
                ? route('front.account.subscriptions.resubmit', $subscription)
                : null,
            'videos_url' => $subscription->product_for === SubscriptionProduct::FOR_PLAN
                && $subscription->frontend_effective_status === UserSubscription::STATUS_ACTIVE
                ? route('front.account.subscriptions.videos', $subscription)
                : null,
        ])->all());
    }

    /** @param array<int, string> $columns */
    private function orderPolymorphicContentQuery(
        Builder $query,
        Request $request,
        string $prefix,
        array $columns,
        string $defaultColumn,
    ): void {
        $column = $columns[$this->orderColumn($request)] ?? $defaultColumn;
        $direction = $this->orderDirection($request);

        if ($column === 'type') {
            $query->orderBy($prefix.'_type', $direction)->orderByDesc('id');

            return;
        }

        if ($column === 'title') {
            $query->orderByRaw(
                "CASE WHEN {$prefix}_type = ? THEN (SELECT title FROM articles WHERE articles.id = {$prefix}_id) WHEN {$prefix}_type = ? THEN (SELECT title FROM magazines WHERE magazines.id = {$prefix}_id) ELSE NULL END {$direction}",
                [Article::class, Magazine::class],
            )->orderByDesc('id');

            return;
        }

        $query->orderBy($column, $direction)->orderByDesc('id');
    }

    private function search(Request $request): string
    {
        return mb_substr(trim((string) $request->input('search.value', '')), 0, 100);
    }

    private function start(Request $request): int
    {
        return max(0, $request->integer('start'));
    }

    private function length(Request $request): int
    {
        $length = $request->integer('length', 10);

        return in_array($length, [10, 25, 50, 100], true) ? $length : 10;
    }

    private function orderColumn(Request $request): int
    {
        return max(0, $request->integer('order.0.column'));
    }

    private function orderDirection(Request $request): string
    {
        return $request->input('order.0.dir') === 'asc' ? 'asc' : 'desc';
    }

    /** @param array<int, array<string, mixed>> $data */
    private function response(Request $request, int $total, int $filtered, array $data): array
    {
        return [
            'draw' => max(0, $request->integer('draw')),
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $data,
        ];
    }
}
