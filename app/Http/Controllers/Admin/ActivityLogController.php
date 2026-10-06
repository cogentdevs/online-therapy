<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ActivityLogsExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ActivityLogFilterRequest;
use App\Models\ActivityLog;
use App\Services\ActivityLogService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Response;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ActivityLogController extends Controller
{
    public function __construct(private ActivityLogService $activityLogService) {}

    public function index(ActivityLogFilterRequest $request): View
    {
        $filters = $request->validated();

        return view('admin.activity-logs.view-activity-logs', [
            'activityLogs' => $this->activityLogService->filteredQuery($filters)
                ->latest('created_at')
                ->paginate(50)
                ->withQueryString(),
            'filters' => $filters,
            ...$this->filterOptions(),
        ]);
    }

    public function show(int $id): View
    {
        return view('admin.activity-logs.view-activity-log-detail', [
            'activityLog' => ActivityLog::query()->findOrFail($id),
        ]);
    }

    public function exportExcel(ActivityLogFilterRequest $request): BinaryFileResponse
    {
        return Excel::download(
            new ActivityLogsExport($this->activityLogService->filteredQuery($request->validated())),
            'activity-logs-'.now()->format('Y-m-d-His').'.xlsx',
        );
    }

    public function exportPdf(ActivityLogFilterRequest $request): Response
    {
        $activityLogs = $this->activityLogService
            ->filteredQuery($request->validated())
            ->latest('created_at')
            ->get();

        return Pdf::loadView('admin.activity-logs.activity-logs-pdf', compact('activityLogs'))
            ->setPaper('a4', 'landscape')
            ->download('activity-logs-'.now()->format('Y-m-d-His').'.pdf');
    }

    /** @return array<string, mixed> */
    private function filterOptions(): array
    {
        return [
            'users' => ActivityLog::query()
                ->whereNotNull('user_id')
                ->select(['user_id', 'user_name'])
                ->distinct()
                ->orderBy('user_name')
                ->get(),
            'roles' => ActivityLog::query()->whereNotNull('role_name')->distinct()->orderBy('role_name')->pluck('role_name'),
            'modules' => ActivityLog::query()->distinct()->orderBy('module')->pluck('module'),
            'actions' => ActivityLog::query()->distinct()->orderBy('action')->pluck('action'),
        ];
    }
}
