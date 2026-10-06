<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\CheckAdvertisingAvailabilityRequest;
use App\Http\Requests\Frontend\StoreAdvertisingRequest;
use App\Models\Ad;
use App\Models\AdRequest;
use App\Services\AdPlacementAvailabilityService;
use App\Services\AdvertisingRequestMailService;
use DateTimeImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AdvertisingRequestController extends Controller
{
    public function __construct(
        private readonly AdPlacementAvailabilityService $availability,
        private readonly AdvertisingRequestMailService $requestMail,
    ) {}

    public function index(Request $request): View
    {
        return view('frontend.advertise', [
            'pagePlacements' => Ad::PAGE_PLACEMENTS,
            'user' => $request->user('web'),
        ]);
    }

    public function availability(CheckAdvertisingAvailabilityRequest $request): JsonResponse
    {
        $dates = $request->validated();
        $placements = [];

        foreach (Ad::PAGE_PLACEMENTS as $pageName => $page) {
            foreach ($page['places'] as $place => $label) {
                $placements[] = [
                    'page_name' => $pageName,
                    'page_label' => $page['label'],
                    'place' => $place,
                    'label' => $label,
                    'available' => $this->availability->isAvailable($pageName, $place, $dates['from_date'], $dates['to_date']),
                ];
            }
        }

        $duration = (new DateTimeImmutable($dates['from_date']))->diff(new DateTimeImmutable($dates['to_date']))->days + 1;

        return response()->json(['duration_days' => $duration, 'placements' => $placements]);
    }

    public function store(StoreAdvertisingRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $placements = $request->selectedPlacements();

        $adRequest = DB::transaction(function () use ($request, $data, $placements): AdRequest {
            foreach ($placements as $placement) {
                if (! $this->availability->isAvailable($placement['page_name'], $placement['place'], $data['from_date'], $data['to_date'])) {
                    throw ValidationException::withMessages([
                        'placements' => 'منتخب تاریخوں میں ایک یا زیادہ تشہیری مقامات دستیاب نہیں۔ براہ کرم دوبارہ انتخاب کریں۔',
                    ]);
                }
            }

            $adRequest = AdRequest::query()->create([
                'user_id' => $request->user('web')->getKey(),
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'company' => $data['company'] ?? null,
                'from_date' => $data['from_date'],
                'to_date' => $data['to_date'],
                'details' => $data['details'] ?? null,
            ]);

            $adRequest->placements()->createMany($placements);

            return $adRequest;
        });

        $this->requestMail->sendForCreatedRequest($adRequest);

        return to_route('front.advertise.success', $adRequest);
    }

    public function success(Request $request, AdRequest $adRequest): View
    {
        abort_unless($adRequest->user_id === $request->user('web')->getKey(), 404);

        return view('frontend.advertise-success', compact('adRequest'));
    }
}
