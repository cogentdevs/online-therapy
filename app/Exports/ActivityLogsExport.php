<?php

namespace App\Exports;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ActivityLogsExport implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping
{
    public function __construct(private Builder $activityLogsQuery) {}

    public function query(): Builder
    {
        return $this->activityLogsQuery->latest('created_at');
    }

    /** @return array<int, string> */
    public function headings(): array
    {
        return ['Date/Time', 'User', 'Role', 'Module', 'Action', 'Subject Type', 'Subject ID', 'Description', 'IP Address', 'Old Values', 'New Values'];
    }

    /** @return array<int, int|string|null> */
    public function map(mixed $row): array
    {
        /** @var ActivityLog $row */
        return [
            $row->created_at?->format('Y-m-d H:i:s'),
            $row->user_name ?? 'System / Deleted User',
            $row->role_name ?? 'Unknown',
            $row->module,
            $row->action,
            $row->subject_type,
            $row->subject_id,
            $row->description,
            $row->ip_address,
            $row->old_values === null ? null : json_encode($row->old_values, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            $row->new_values === null ? null : json_encode($row->new_values, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        ];
    }
}
