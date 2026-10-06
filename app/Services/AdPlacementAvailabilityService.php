<?php

namespace App\Services;

use App\Models\Ad;
use App\Models\AdRequestPlacement;
use DateTimeImmutable;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Builder;
use InvalidArgumentException;

class AdPlacementAvailabilityService
{
    public function isValidPlacement(string $pageName, string $place): bool
    {
        return array_key_exists($place, Ad::placementsForPage($pageName));
    }

    public function isAvailable(
        string $pageName,
        string $place,
        string|DateTimeInterface $fromDate,
        string|DateTimeInterface $toDate,
        ?int $excludeRequestId = null,
        ?int $excludePlacementId = null,
    ): bool {
        if (! $this->isValidPlacement($pageName, $place)) {
            throw new InvalidArgumentException('The advertising page and place combination is invalid.');
        }

        $from = $this->dateString($fromDate);
        $to = $this->dateString($toDate);

        if ($from > $to) {
            throw new InvalidArgumentException('The advertising end date must be on or after the start date.');
        }

        // An actual Ad is a booking even when display is paused. Null dates mean an open-ended booking.
        $actualAdExists = Ad::query()
            ->where('page_name', $pageName)
            ->where('place', $place)
            ->where(fn (Builder $query): Builder => $query->whereNull('start_date')->orWhereDate('start_date', '<=', $to))
            ->where(fn (Builder $query): Builder => $query->whereNull('expiry_date')->orWhereDate('expiry_date', '>=', $from))
            ->exists();

        if ($actualAdExists) {
            return false;
        }

        // Published placements are represented by actual Ads in the later workflow; only confirmed reservations block here.
        return ! AdRequestPlacement::query()
            ->where('page_name', $pageName)
            ->where('place', $place)
            ->where('status', 'confirmed')
            ->when($excludePlacementId !== null, fn (Builder $query): Builder => $query->where('id', '!=', $excludePlacementId))
            ->when($excludeRequestId !== null, fn (Builder $query): Builder => $query->where('ad_request_id', '!=', $excludeRequestId))
            ->whereHas('adRequest', function (Builder $query) use ($from, $to): void {
                $query->whereNotIn('status', ['cancelled', 'rejected'])
                    ->whereDate('from_date', '<=', $to)
                    ->whereDate('to_date', '>=', $from);
            })
            ->exists();
    }

    private function dateString(string|DateTimeInterface $date): string
    {
        if ($date instanceof DateTimeInterface) {
            return $date->format('Y-m-d');
        }

        $parsed = DateTimeImmutable::createFromFormat('!Y-m-d', $date);

        if ($parsed === false || $parsed->format('Y-m-d') !== $date) {
            throw new InvalidArgumentException('Advertising dates must use the Y-m-d format.');
        }

        return $date;
    }
}
