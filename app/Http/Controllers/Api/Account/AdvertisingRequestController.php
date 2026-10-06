<?php

namespace App\Http\Controllers\Api\Account;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Account\AdvertisingRequestListRequest;
use App\Http\Requests\Api\StoreAdvertisingRequest;
use App\Models\Ad;
use App\Models\AdRequest;
use App\Models\AdRequestPlacement;
use App\Services\AdPlacementAvailabilityService;
use App\Services\AdvertisingRequestMailService;
use App\Services\Api\MobileAccountReadService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AdvertisingRequestController extends Controller
{
    public function __construct(
        private readonly AdPlacementAvailabilityService $availability,
        private readonly AdvertisingRequestMailService $mailService,
        private readonly MobileAccountReadService $accountReadService,
    ) {}

    public function store(StoreAdvertisingRequest $request): JsonResponse
    {
        $data = $request->validated();
        $placements = $request->selectedPlacements();

        $adRequest = DB::transaction(function () use ($request, $data, $placements): AdRequest {
            foreach ($placements as $placement) {
                if (! $this->availability->isAvailable($placement['page_name'], $placement['place'], $data['from_date'], $data['to_date'])) {
                    throw ValidationException::withMessages([
                        'placements' => 'One or more selected advertising placements are unavailable for these dates.',
                    ]);
                }
            }

            $record = AdRequest::query()->create([
                'user_id' => $request->user()->getKey(),
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'company' => $data['company'],
                'from_date' => $data['from_date'],
                'to_date' => $data['to_date'],
                'details' => $data['details'] ?? null,
            ]);
            $record->placements()->createMany($placements);

            return $record->load('placements');
        });

        $this->mailService->sendForCreatedRequest($adRequest);

        return response()->json([
            'success' => true,
            'message' => 'Your advertising request has been received successfully.',
            'data' => ['advertising_request' => $this->advertisingRequest($adRequest)],
        ], 201);
    }

    public function index(AdvertisingRequestListRequest $request): JsonResponse
    {
        $filters = $request->validated();
        $query = $request->user()->adRequests()->withCount('placements');
        $search = trim((string) ($filters['search'] ?? ''));

        if ($search !== '') {
            $query->where(function (Builder $query) use ($search): void {
                $query->where('request_no', 'like', '%'.$search.'%')
                    ->orWhere('company', 'like', '%'.$search.'%')
                    ->orWhere('status', 'like', '%'.$search.'%');
            });
        }

        $column = match ($filters['sort'] ?? 'date') {
            'request_no' => 'request_no',
            'company' => 'company',
            'status' => 'status',
            'from_date' => 'from_date',
            'to_date' => 'to_date',
            default => 'created_at',
        };
        $records = $query->orderBy($column, $filters['direction'] ?? 'desc')
            ->orderByDesc('id')->paginate(10)->withQueryString();

        return response()->json(['success' => true, 'data' => [
            'advertising_requests' => collect($records->items())->map(fn (AdRequest $record): array => $this->advertisingRequestSummary($record))->all(),
            'pagination' => $this->accountReadService->pagination($records),
        ]]);
    }

    public function show(Request $request, int $adRequest): JsonResponse
    {
        $record = $request->user()->adRequests()->with('placements.ad')->findOrFail($adRequest);

        return response()->json(['success' => true, 'data' => ['advertising_request' => $this->advertisingRequest($record)]]);
    }

    /** @return array<string, mixed> */
    private function advertisingRequestSummary(AdRequest $request): array
    {
        return [
            'id' => $request->id,
            'request_no' => $request->request_no,
            'company' => $request->company,
            'from_date' => $request->from_date?->toDateString(),
            'to_date' => $request->to_date?->toDateString(),
            'duration_days' => $request->durationDays(),
            'status' => $request->status,
            'placements_count' => $request->placements_count ?? $request->placements->count(),
            'submitted_at' => $request->created_at?->toIso8601String(),
        ];
    }

    /** @return array<string, mixed> */
    private function advertisingRequest(AdRequest $request): array
    {
        $request->loadMissing('placements.ad');

        return [
            ...$this->advertisingRequestSummary($request),
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'details' => $request->details,
            'placements' => $request->placements->map(fn (AdRequestPlacement $placement): array => $this->placement($placement))->values()->all(),
        ];
    }

    /** @return array<string, mixed> */
    private function placement(AdRequestPlacement $placement): array
    {
        $page = Ad::PAGE_PLACEMENTS[$placement->page_name] ?? null;
        $ad = $placement->ad;
        $clickTrackingAvailable = $ad !== null && $this->clickTrackingAvailable($ad);

        return [
            'id' => $placement->id,
            'page_name' => $placement->page_name,
            'page_label' => $page['label'] ?? $placement->page_name,
            'place' => $placement->place,
            'placement_label' => $page['places'][$placement->place] ?? $placement->place,
            'status' => $placement->status,
            'ad' => $ad === null ? null : [
                'id' => $ad->id,
                'title' => $ad->title,
                'status' => $this->adStatus($ad),
                'start_date' => $ad->start_date?->toDateString(),
                'expiry_date' => $ad->expiry_date?->toDateString(),
                'click_count' => $clickTrackingAvailable ? $ad->click_count : null,
                'click_tracking_available' => $clickTrackingAvailable,
            ],
        ];
    }

    private function adStatus(Ad $ad): string
    {
        if (! $ad->isActive) {
            return 'inactive';
        }

        if ($ad->expiry_date?->isBefore(today())) {
            return 'expired';
        }

        if ($ad->start_date?->isAfter(today())) {
            return 'scheduled';
        }

        return 'active';
    }

    private function clickTrackingAvailable(Ad $ad): bool
    {
        if (blank($ad->ad_image) || filled($ad->google_ad_code) || ! is_string($ad->ad_url)) {
            return false;
        }

        $scheme = strtolower((string) parse_url($ad->ad_url, PHP_URL_SCHEME));

        return filter_var($ad->ad_url, FILTER_VALIDATE_URL) !== false
            && in_array($scheme, ['http', 'https'], true);
    }
}
