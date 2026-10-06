<?php

namespace App\Http\Controllers\Admin;

use App\Exports\SiteAnalyticsDetailExport;
use App\Exports\SiteAnalyticsSummaryExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SiteAnalyticsRequest;
use App\Services\SiteAnalyticsService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SiteAnalyticsController extends Controller
{
    public function __construct(private readonly SiteAnalyticsService $siteAnalyticsService) {}

    public function index(SiteAnalyticsRequest $request): View
    {
        $filters = $request->filters();

        return view('admin.site-analytics.index', [
            'filters' => $filters,
            'types' => $this->siteAnalyticsService->types(),
            'selectedType' => $this->siteAnalyticsService->type($filters['type']),
        ]);
    }

    public function data(SiteAnalyticsRequest $request): JsonResponse
    {
        return response()->json($this->siteAnalyticsService->aggregateData($request, $request->filters()));
    }

    public function detail(SiteAnalyticsRequest $request, string $type, string $identifier): View
    {
        $filters = [...$request->filters(), 'type' => $type];
        abort_unless($this->siteAnalyticsService->detailExists($type, $identifier, $filters), 404);
        $context = $this->siteAnalyticsService->detailContext($type, $identifier, $filters);

        return view('admin.site-analytics.detail', compact('context', 'filters', 'identifier', 'type'));
    }

    public function detailData(SiteAnalyticsRequest $request, string $type, string $identifier): JsonResponse
    {
        $filters = [...$request->filters(), 'type' => $type];
        abort_unless($this->siteAnalyticsService->detailExists($type, $identifier, $filters), 404);

        return response()->json($this->siteAnalyticsService->detailData($request, $type, $identifier, $filters));
    }

    public function export(SiteAnalyticsRequest $request): BinaryFileResponse
    {
        $filters = $request->filters();

        return Excel::download(
            new SiteAnalyticsSummaryExport($this->siteAnalyticsService->aggregateExportRows($filters['type'], $filters)),
            'site-analytics-'.$filters['type'].'-'.now()->format('Y-m-d-His').'.xlsx',
        );
    }

    public function detailExport(SiteAnalyticsRequest $request, string $type, string $identifier): BinaryFileResponse
    {
        $filters = [...$request->filters(), 'type' => $type];
        abort_unless($this->siteAnalyticsService->detailExists($type, $identifier, $filters), 404);

        return Excel::download(
            new SiteAnalyticsDetailExport($this->siteAnalyticsService->detailExportRows($type, $identifier, $filters)),
            'site-analytics-'.$type.'-'.$identifier.'-'.now()->format('Y-m-d-His').'.xlsx',
        );
    }
}
