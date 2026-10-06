<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="width:100%;margin:0 0 22px;background:#F8FAFC;border:1px solid #DDE5EE;border-collapse:separate;border-spacing:0;border-radius:8px;">
    <tr><td colspan="2" style="padding:14px 18px;background:#EAF0F7;color:#315782;font-size:16px;font-weight:700;text-align:right;">درخواست کی تفصیلات</td></tr>
    <tr><td style="padding:12px 18px;color:#737078;">درخواست نمبر</td><td dir="ltr" style="padding:12px 18px;color:#3F6CA1;font-weight:700;text-align:left;">{{ $adRequest->request_no }}</td></tr>
    <tr><td style="padding:12px 18px;color:#737078;">نام</td><td style="padding:12px 18px;text-align:right;overflow-wrap:anywhere;">{{ $adRequest->name }}</td></tr>
    @if ($showContact ?? false)
        <tr><td style="padding:12px 18px;color:#737078;">ای میل</td><td dir="ltr" style="padding:12px 18px;text-align:left;overflow-wrap:anywhere;">{{ $adRequest->email }}</td></tr>
        <tr><td style="padding:12px 18px;color:#737078;">فون نمبر</td><td dir="ltr" style="padding:12px 18px;text-align:left;">{{ $adRequest->phone }}</td></tr>
    @endif
    @if ($adRequest->company)
        <tr><td style="padding:12px 18px;color:#737078;">کمپنی / برانڈ</td><td style="padding:12px 18px;text-align:right;overflow-wrap:anywhere;">{{ $adRequest->company }}</td></tr>
    @endif
    <tr><td style="padding:12px 18px;color:#737078;">آغاز کی تاریخ</td><td dir="ltr" style="padding:12px 18px;text-align:left;">{{ $adRequest->from_date->format('d M Y') }}</td></tr>
    <tr><td style="padding:12px 18px;color:#737078;">اختتام کی تاریخ</td><td dir="ltr" style="padding:12px 18px;text-align:left;">{{ $adRequest->to_date->format('d M Y') }}</td></tr>
    <tr><td style="padding:12px 18px;color:#737078;">کل مدت</td><td style="padding:12px 18px;text-align:right;">{{ $adRequest->durationDays() }} دن</td></tr>
    <tr><td style="padding:12px 18px;color:#737078;">حیثیت</td><td style="padding:12px 18px;color:#3F6CA1;font-weight:700;text-align:right;">{{ $displayStatus ?? 'زیرِ جائزہ (Pending)' }}</td></tr>
    @if ($showContact ?? false)
        <tr><td style="padding:12px 18px;color:#737078;">جمع کرانے کا وقت</td><td dir="ltr" style="padding:12px 18px;text-align:left;">{{ $adRequest->created_at?->format('d M Y, h:i A') }}</td></tr>
    @endif
</table>

<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="width:100%;margin:0 0 22px;background:#F8FAFC;border:1px solid #DDE5EE;border-collapse:separate;border-spacing:0;border-radius:8px;">
    <tr><td style="padding:14px 18px;background:#EAF0F7;color:#315782;font-size:16px;font-weight:700;text-align:right;">منتخب تشہیری مقامات</td></tr>
    @foreach ($adRequest->placements as $placement)
        <tr><td style="padding:11px 18px;border-top:1px solid #DDE5EE;text-align:right;">{{ $placement->displayLabel() }}</td></tr>
    @endforeach
</table>

@if ($adRequest->details)
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="width:100%;margin:0 0 22px;background:#F8FAFC;border:1px solid #DDE5EE;border-collapse:separate;border-spacing:0;border-radius:8px;">
        <tr><td style="padding:14px 18px;background:#EAF0F7;color:#315782;font-size:16px;font-weight:700;text-align:right;">مزید تفصیلات</td></tr>
        <tr><td style="padding:14px 18px;line-height:1.9;text-align:right;white-space:pre-line;overflow-wrap:anywhere;">{{ $adRequest->details }}</td></tr>
    </table>
@endif
