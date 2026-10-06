<?php

namespace App\Services;

use App\Models\AdRequest;
use App\Models\AdRequestPlacement;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AdRequestWorkflowService
{
    public function __construct(
        private readonly AdPlacementAvailabilityService $availability,
        private readonly AdvertisingRequestMailService $mail,
    ) {}

    public function changeRequestStatus(AdRequest $adRequest, string $action): void
    {
        DB::transaction(function () use ($adRequest, $action): void {
            $request = AdRequest::query()->lockForUpdate()->findOrFail($adRequest->id);
            $allowed = match ($action) {
                'quote_sent', 'rejected' => ['pending', 'quote_sent'],
                'cancelled' => ['pending', 'quote_sent', 'confirmed'],
                default => [],
            };

            if (! in_array($request->status, $allowed, true) || $request->status === $action) {
                throw ValidationException::withMessages(['status' => 'This request status transition is not allowed.']);
            }

            if ($action === 'cancelled' && $request->placements()->where('status', 'published')->exists()) {
                throw ValidationException::withMessages(['status' => 'A request with a published advertisement cannot be cancelled.']);
            }

            if ($action === 'quote_sent') {
                $request->placements()->where('status', 'pending')->get()->each(function (AdRequestPlacement $placement): void {
                    $placement->status = 'quoted';
                    $placement->save();
                });
            } else {
                $request->placements()->whereIn('status', ['pending', 'quoted', 'confirmed', 'conflicted'])->get()->each(function (AdRequestPlacement $placement): void {
                    $placement->status = 'cancelled';
                    $placement->save();
                });
            }

            $request->status = $action;
            $request->save();
        });

        $this->mail->sendStatusUpdate($adRequest, $action);
    }

    public function confirmPlacement(AdRequestPlacement $placement): void
    {
        DB::transaction(function () use ($placement): void {
            $request = AdRequest::query()->findOrFail($placement->ad_request_id);
            $this->lockOverlappingRequests($request);
            $current = AdRequestPlacement::query()->lockForUpdate()->findOrFail($placement->id);
            $request->refresh();

            if (! in_array($request->status, ['pending', 'quote_sent', 'confirmed'], true)
                || ! in_array($current->status, ['pending', 'quoted', 'conflicted'], true)) {
                throw ValidationException::withMessages(['status' => 'This placement cannot be confirmed.']);
            }

            if ($request->to_date->isBefore(today())) {
                throw ValidationException::withMessages(['status' => 'This request has already expired.']);
            }

            if (! $this->availability->isAvailable($current->page_name, $current->place, $request->from_date, $request->to_date, excludePlacementId: $current->id)) {
                throw ValidationException::withMessages(['status' => 'This placement is no longer available for the requested dates.']);
            }

            $current->status = 'confirmed';
            $current->save();
            $this->recalculate($request);

            AdRequestPlacement::query()
                ->where('id', '!=', $current->id)
                ->where('page_name', $current->page_name)
                ->where('place', $current->place)
                ->whereIn('status', ['pending', 'quoted'])
                ->whereHas('adRequest', fn (Builder $query): Builder => $query
                    ->whereNotIn('status', ['rejected', 'cancelled'])
                    ->whereDate('from_date', '<=', $request->to_date)
                    ->whereDate('to_date', '>=', $request->from_date))
                ->get()->each(function (AdRequestPlacement $other): void {
                    $other->status = 'conflicted';
                    $other->save();
                });
        });

        $this->mail->sendStatusUpdate($placement->adRequest, 'confirmed', $placement);
    }

    public function lockOverlappingRequests(AdRequest $request): void
    {
        AdRequest::query()->whereDate('from_date', '<=', $request->to_date)
            ->whereDate('to_date', '>=', $request->from_date)
            ->orderBy('id')->lockForUpdate()->get(['id']);
    }

    public function recalculate(AdRequest $request): void
    {
        $statuses = $request->placements()->pluck('status');
        $active = $statuses->filter(fn (string $status): bool => ! in_array($status, ['conflicted', 'cancelled'], true));

        if ($active->isNotEmpty() && $active->every(fn (string $status): bool => $status === 'published')) {
            $request->status = 'published';
        } elseif ($statuses->contains('confirmed') || $statuses->contains('published')) {
            $request->status = 'confirmed';
        } elseif (! in_array($request->status, ['quote_sent', 'rejected', 'cancelled'], true)) {
            $request->status = 'pending';
        }

        if ($request->isDirty('status')) {
            $request->save();
        }
    }
}
