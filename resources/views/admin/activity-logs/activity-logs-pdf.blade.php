<!doctype html>
<html lang="en">
<head><meta charset="utf-8"><title>Activity Logs</title><style>body{font-family:DejaVu Sans,sans-serif;font-size:9px;color:#222}h1{font-size:18px}table{border-collapse:collapse;width:100%}th,td{border:1px solid #bbb;padding:5px;vertical-align:top}th{background:#eee;text-align:left}</style></head>
<body>
    <h1>Admin Activity Logs</h1>
    <p>Generated: {{ now()->format('d M Y, h:i A') }}</p>
    <table>
        <thead><tr><th>Date/Time</th><th>User</th><th>Role</th><th>Module</th><th>Action</th><th>Description</th><th>IP</th></tr></thead>
        <tbody>@forelse ($activityLogs as $activityLog)<tr><td>{{ $activityLog->created_at?->format('Y-m-d H:i:s') }}</td><td>{{ $activityLog->user_name ?? 'System / Deleted User' }}</td><td>{{ $activityLog->role_name ?? 'Unknown' }}</td><td>{{ $activityLog->module }}</td><td>{{ $activityLog->action }}</td><td>{{ $activityLog->description }}</td><td>{{ $activityLog->ip_address }}</td></tr>@empty<tr><td colspan="7">No records found.</td></tr>@endforelse</tbody>
    </table>
</body>
</html>
