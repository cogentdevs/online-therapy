<?php

namespace App\Services;

use App\Models\SiteVisit;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class SiteAnalyticsService
{
    /** @return array<string, array<string, mixed>> */
    public function types(): array
    {
        return (array) config('site_analytics.types', []);
    }

    /** @return array<string, mixed> */
    public function type(string $key): array
    {
        $definition = $this->types()[$key] ?? null;

        if (! is_array($definition)) {
            throw ValidationException::withMessages(['type' => 'The selected analytics type is invalid.']);
        }

        return $definition;
    }

    /** @param array<string, mixed> $filters */
    public function aggregateData(Request $request, array $filters): array
    {
        $typeKey = (string) ($filters['type'] ?? 'pages');
        $definition = $this->type($typeKey);
        $query = $this->aggregateQuery($typeKey, $filters);
        $total = $this->aggregateQuery($typeKey, $filters)->get()->count();
        $search = mb_substr(trim((string) $request->input('search.value', '')), 0, 100);

        if ($search !== '') {
            $this->applyAggregateSearch($query, $typeKey, $definition, $search);
        }

        $filtered = (clone $query)->get()->count();
        $orderColumns = ['group_key', 'resolved_name', 'visit_count', 'last_started_at', 'last_started_at'];
        $orderColumn = $orderColumns[max(0, $request->integer('order.0.column'))] ?? 'last_started_at';
        $direction = $request->input('order.0.dir') === 'asc' ? 'asc' : 'desc';
        $query->orderBy($orderColumn, $direction)->orderBy('group_key');
        $rows = $query->offset(max(0, $request->integer('start')))
            ->limit($this->length($request))
            ->get();

        $names = $this->names($definition, $rows->pluck('group_key'));
        $latestVisits = $this->latestVisits($typeKey, $definition, $filters, $rows->pluck('group_key'));

        return [
            'draw' => max(0, $request->integer('draw')),
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $rows->values()->map(function ($row, int $index) use ($request, $typeKey, $definition, $filters, $names, $latestVisits): array {
                $key = (string) $row->group_key;
                $latest = $latestVisits->get($key);

                return [
                    'serial' => max(0, $request->integer('start')) + $index + 1,
                    'name' => $this->displayName($typeKey, $key, $names),
                    'visit_count' => (int) $row->visit_count,
                    'last_location' => $this->location($latest),
                    'last_visited_at' => $latest?->started_at?->format('d M Y, h:i A') ?? 'N/A',
                    'detail_url' => route('admin.site-analytics.detail', ['type' => $typeKey, 'identifier' => $key, ...$this->dateParameters($filters)]),
                    'column_label' => $definition['column_label'],
                ];
            })->all(),
        ];
    }

    /** @param array<string, mixed> $filters */
    public function detailData(Request $request, string $typeKey, string $identifier, array $filters): array
    {
        $definition = $this->type($typeKey);
        $query = $this->detailQuery($typeKey, $identifier, $filters)->with('user:id,name');
        $total = (clone $query)->count();
        $search = mb_substr(trim((string) $request->input('search.value', '')), 0, 100);

        if ($search !== '') {
            $query->where(function (Builder $query) use ($search): void {
                $query->where('country', 'like', '%'.$search.'%')
                    ->orWhere('city', 'like', '%'.$search.'%')
                    ->orWhere('region', 'like', '%'.$search.'%')
                    ->orWhere('device', 'like', '%'.$search.'%')
                    ->orWhere('browser', 'like', '%'.$search.'%')
                    ->orWhere('os', 'like', '%'.$search.'%');
            });
        }

        $filtered = (clone $query)->count();
        $columns = [1 => 'user_id', 2 => 'country', 3 => 'city', 4 => 'region', 5 => 'device_type', 6 => 'browser', 7 => 'os', 8 => 'duration_seconds', 9 => 'started_at'];
        $column = $columns[max(0, $request->integer('order.0.column'))] ?? 'started_at';
        $direction = $request->input('order.0.dir') === 'asc' ? 'asc' : 'desc';
        $visits = $query->orderBy($column, $direction)->orderByDesc('id')
            ->offset(max(0, $request->integer('start')))
            ->limit($this->length($request))
            ->get();

        return [
            'draw' => max(0, $request->integer('draw')),
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $visits->values()->map(fn (SiteVisit $visit, int $index): array => [
                'serial' => max(0, $request->integer('start')) + $index + 1,
                'visitor' => $visit->user_id === null ? 'Guest' : 'Registered'.($visit->user?->name ? ' - '.$visit->user->name : ''),
                'country' => $visit->country ?: 'N/A',
                'city' => $visit->city ?: 'N/A',
                'region' => $visit->region ?: 'N/A',
                'device' => $this->combined($visit->device, $visit->device_type),
                'browser' => $this->combined($visit->browser, $visit->browser_version),
                'os' => $this->combined($visit->os, $visit->os_version),
                'duration' => $this->humanDuration($visit->duration_seconds),
                'started_at' => $visit->started_at?->format('d M Y, h:i A') ?? 'N/A',
            ])->all(),
        ];
    }

    /** @param array<string, mixed> $filters */
    public function detailContext(string $typeKey, string $identifier, array $filters): array
    {
        $definition = $this->type($typeKey);
        $names = $this->names($definition, collect([$identifier]));

        return [
            'name' => $this->displayName($typeKey, $identifier, $names),
            'typeLabel' => $definition['label'],
            'summaries' => collect(['country', 'city', 'region'])->mapWithKeys(fn (string $field): array => [
                $field => $this->mostVisitedLocation($typeKey, $identifier, $filters, $field),
            ])->all(),
        ];
    }

    /** @param array<string, mixed> $filters */
    public function detailExists(string $typeKey, string $identifier, array $filters): bool
    {
        return $this->detailQuery($typeKey, $identifier, [...$filters, 'from_date' => null, 'to_date' => null])->exists();
    }

    /** @param array<string, mixed> $filters */
    public function aggregateExportRows(string $typeKey, array $filters): Collection
    {
        $definition = $this->type($typeKey);
        $rows = $this->aggregateQuery($typeKey, $filters)->orderByDesc('last_started_at')->get();
        $names = $this->names($definition, $rows->pluck('group_key'));
        $latest = $this->latestVisits($typeKey, $definition, $filters, $rows->pluck('group_key'));

        return $rows->map(function ($row) use ($typeKey, $definition, $names, $latest): array {
            $key = (string) $row->group_key;
            $visit = $latest->get($key);

            return [$definition['label'], $this->displayName($typeKey, $key, $names), (int) $row->visit_count, $visit?->country, $visit?->city, $visit?->started_at?->format('Y-m-d H:i:s')];
        });
    }

    /** @param array<string, mixed> $filters */
    public function detailExportRows(string $typeKey, string $identifier, array $filters): Collection
    {
        return $this->detailQuery($typeKey, $identifier, $filters)->with('user:id,name')->latest('started_at')->get()->map(fn (SiteVisit $visit): array => [
            $visit->user_id === null ? 'Guest' : 'Registered',
            $visit->country, $visit->city, $visit->region, $visit->device, $visit->browser,
            $visit->browser_version, $visit->os, $visit->os_version, $visit->duration_seconds,
            $this->humanDuration($visit->duration_seconds), $visit->started_at?->format('Y-m-d H:i:s'),
        ]);
    }

    public function humanDuration(int $seconds): string
    {
        $seconds = max(0, $seconds);
        $hours = intdiv($seconds, 3600);
        $minutes = intdiv($seconds % 3600, 60);
        $remaining = $seconds % 60;

        return collect([$hours ? $hours.' hr' : null, $minutes ? $minutes.' min' : null, ($remaining || $seconds === 0) ? $remaining.' sec' : null])->filter()->implode(' ');
    }

    /** @param array<string, mixed> $filters */
    private function aggregateQuery(string $typeKey, array $filters): Builder
    {
        $definition = $this->type($typeKey);
        $query = $this->scopedQuery($typeKey, $definition, $filters);

        if ($typeKey === 'pages') {
            return $query
                ->selectRaw('site_visits.page_key as group_key, site_visits.page_key as resolved_name, COUNT(*) as visit_count, MAX(site_visits.started_at) as last_started_at')
                ->groupBy('site_visits.page_key');
        }

        /** @var class-string<Model> $modelClass */
        $modelClass = $definition['model'];
        $model = new $modelClass;
        $table = $model->getTable();
        $titleColumn = $definition['title_column'];

        return $query
            ->leftJoin($table, 'site_visits.visitable_id', '=', $table.'.'.$model->getKeyName())
            ->selectRaw('site_visits.visitable_id as group_key, MAX('.$table.'.'.$titleColumn.') as resolved_name, COUNT(*) as visit_count, MAX(site_visits.started_at) as last_started_at')
            ->groupBy('site_visits.visitable_id');
    }

    /** @param array<string, mixed> $definition @param array<string, mixed> $filters */
    private function scopedQuery(string $typeKey, array $definition, array $filters): Builder
    {
        $query = SiteVisit::query();

        if ($typeKey === 'pages') {
            $query->whereNull('visitable_type')->whereNull('visitable_id');
        } else {
            /** @var class-string<Model> $modelClass */
            $modelClass = $definition['model'];
            $query->where('visitable_type', (new $modelClass)->getMorphClass());
        }

        return $this->applyDates($query, $filters);
    }

    /** @param array<string, mixed> $filters */
    private function detailQuery(string $typeKey, string $identifier, array $filters): Builder
    {
        $definition = $this->type($typeKey);
        $query = $this->scopedQuery($typeKey, $definition, $filters);

        if ($typeKey === 'pages') {
            abort_unless(array_key_exists($identifier, (array) config('site_analytics.page_labels', [])), 404);

            return $query->where('page_key', $identifier);
        }

        abort_unless(ctype_digit($identifier) && (int) $identifier > 0, 404);

        return $query->where('visitable_id', (int) $identifier);
    }

    /** @param array<string, mixed> $filters */
    private function applyDates(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['from_date'] ?? null, fn (Builder $query, string $date): Builder => $query->where('started_at', '>=', Carbon::parse($date)->startOfDay()))
            ->when($filters['to_date'] ?? null, fn (Builder $query, string $date): Builder => $query->where('started_at', '<=', Carbon::parse($date)->endOfDay()));
    }

    /** @param array<string, mixed> $definition */
    private function applyAggregateSearch(Builder $query, string $typeKey, array $definition, string $search): void
    {
        if ($typeKey === 'pages') {
            $matching = collect((array) config('site_analytics.page_labels', []))->filter(fn (string $label, string $key): bool => Str::contains(Str::lower($label.' '.$key), Str::lower($search)))->keys();
            $query->whereIn('page_key', $matching);

            return;
        }

        /** @var class-string<Model> $modelClass */
        $modelClass = $definition['model'];
        $ids = $modelClass::query()->where($definition['title_column'], 'like', '%'.$search.'%')->pluck((new $modelClass)->getKeyName());
        $query->whereIn('visitable_id', $ids);
    }

    /** @param array<string, mixed> $definition */
    private function names(array $definition, Collection $ids): Collection
    {
        if ($definition['model'] === null) {
            return collect();
        }

        /** @var class-string<Model> $modelClass */
        $modelClass = $definition['model'];

        return $modelClass::query()->whereKey($ids->all())->pluck($definition['title_column'], (new $modelClass)->getKeyName());
    }

    private function displayName(string $typeKey, string $identifier, Collection $names): string
    {
        if ($typeKey === 'pages') {
            return (string) config('site_analytics.page_labels.'.$identifier, Str::headline($identifier));
        }

        return (string) ($names->get($identifier) ?? 'Deleted / Unavailable Content');
    }

    /** @param array<string, mixed> $definition @param array<string, mixed> $filters */
    private function latestVisits(string $typeKey, array $definition, array $filters, Collection $keys): Collection
    {
        if ($keys->isEmpty()) {
            return collect();
        }

        $groupColumn = $typeKey === 'pages' ? 'page_key' : 'visitable_id';
        $latestTimes = $this->scopedQuery($typeKey, $definition, $filters)
            ->whereIn($groupColumn, $keys->all())
            ->selectRaw($groupColumn.' as latest_group_key, MAX(started_at) as latest_started_at')
            ->groupBy($groupColumn);

        return $this->scopedQuery($typeKey, $definition, $filters)
            ->select('site_visits.*')
            ->joinSub($latestTimes, 'latest_visits', function ($join) use ($groupColumn): void {
                $join->on('site_visits.'.$groupColumn, '=', 'latest_visits.latest_group_key')
                    ->on('site_visits.started_at', '=', 'latest_visits.latest_started_at');
            })
            ->orderByDesc('site_visits.started_at')->orderByDesc('site_visits.id')->get()
            ->unique($groupColumn)->keyBy(fn (SiteVisit $visit): string => (string) $visit->{$groupColumn});
    }

    private function location(?SiteVisit $visit): string
    {
        return collect([$visit?->country, $visit?->city])->filter()->implode(', ') ?: 'N/A';
    }

    /** @param array<string, mixed> $filters */
    private function mostVisitedLocation(string $typeKey, string $identifier, array $filters, string $field): array
    {
        $row = $this->detailQuery($typeKey, $identifier, $filters)->whereNotNull($field)->where($field, '<>', '')
            ->selectRaw($field.' as value, COUNT(*) as visit_count')->groupBy($field)
            ->orderByDesc('visit_count')->orderBy($field)->first();

        return ['value' => $row?->value ?? 'N/A', 'count' => (int) ($row?->visit_count ?? 0)];
    }

    private function combined(?string $name, ?string $version): string
    {
        return collect([$name, $version])->filter()->implode(' - ') ?: 'N/A';
    }

    private function length(Request $request): int
    {
        $length = $request->integer('length', 10);

        return in_array($length, [10, 25, 50, 100], true) ? $length : 10;
    }

    /** @param array<string, mixed> $filters */
    private function dateParameters(array $filters): array
    {
        return array_filter(['from_date' => $filters['from_date'] ?? null, 'to_date' => $filters['to_date'] ?? null]);
    }
}
