<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdRequest;
use App\Models\AdRequestPlacement;
use App\Services\AdPlacementAvailabilityService;
use App\Services\AdRequestWorkflowService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdvertisingRequestController extends Controller
{
    public function index(): View
    {
        return view('admin.advertising-requests.index', [
            'requests' => AdRequest::query()->with('user')->withCount('placements')->latest()->get(),
        ]);
    }

    public function show(AdRequest $adRequest, AdPlacementAvailabilityService $availability): View
    {
        $adRequest->load(['placements.ad', 'user']);
        $availabilityByPlacement = $adRequest->placements->mapWithKeys(fn (AdRequestPlacement $placement): array => [
            $placement->id => $availability->isAvailable(
                $placement->page_name,
                $placement->place,
                $adRequest->from_date,
                $adRequest->to_date,
                excludePlacementId: $placement->id,
            ),
        ]);

        return view('admin.advertising-requests.show', compact('adRequest', 'availabilityByPlacement'));
    }

    public function changeStatus(Request $request, AdRequest $adRequest, AdRequestWorkflowService $workflow): RedirectResponse
    {
        $data = $request->validate(['action' => ['required', 'string', Rule::in(['quote_sent', 'rejected', 'cancelled'])]]);
        $workflow->changeRequestStatus($adRequest, $data['action']);

        return to_route('admin.advertising-requests.show', $adRequest)->with('status', 'Request status updated.');
    }

    public function confirmPlacement(AdRequest $adRequest, AdRequestPlacement $placement, AdRequestWorkflowService $workflow): RedirectResponse
    {
        abort_unless($placement->ad_request_id === $adRequest->id, 404);
        $workflow->confirmPlacement($placement);

        return to_route('admin.advertising-requests.show', $adRequest)->with('status', 'Placement confirmed.');
    }
}
