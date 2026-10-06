@php
    $paymentMethod = match ($userSubscription->payment_method) {
        'easypaisa' => 'Easypaisa', 'jazzcash' => 'JazzCash', 'card' => 'Master / Visa Card',
        default => $userSubscription->payment_method,
    };
    $modules = $userSubscription->subscriptionTypes->map(fn ($type) => [
        'original' => $type->name,
        'label' => match (strtolower(trim((string) $type->name))) {
            'magazine', 'magazines' => 'ہفتہ وار میگزین', 'article', 'articles' => 'مضامین', 'audio' => 'آڈیو',
            default => $type->name,
        },
    ]);
@endphp

<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="width:100%;margin:0;background:#F8FAFC;border:1px solid #DDE5EE;border-collapse:separate;border-spacing:0;border-radius:8px;">
    <tr><td style="padding:16px 18px;border-bottom:1px solid #DDE5EE;">
        <div style="margin:0 0 9px;color:#737078;font-size:12px;">شامل ماڈیولز</div>
        <div style="direction:rtl;text-align:right;line-height:2.3;">
            @forelse ($modules as $module)
                <span title="{{ $module['original'] }}" style="display:inline-block;margin:2px 0 2px 5px;padding:4px 10px;background:#EAF0F7;color:#315782;border-radius:14px;font-size:12px;font-weight:700;">{{ $module['label'] }}</span>
            @empty
                <span style="color:#737078;font-size:13px;">کوئی ماڈیول درج نہیں</span>
            @endforelse
        </div>
    </td></tr>
    <tr><td style="padding:18px;border-bottom:1px solid #DDE5EE;text-align:center;">
        <div style="margin:0 0 5px;color:#737078;font-size:12px;">کل ادا شدہ رقم</div>
        <div dir="ltr" style="color:#3F6CA1;font-size:22px;line-height:1.5;font-weight:700;text-align:center;">{{ $userSubscription->currency?->code ?: $userSubscription->currency?->symbol }} {{ number_format((float) $userSubscription->total, 2) }}</div>
    </td></tr>
    <tr><td style="padding:14px 18px;border-bottom:1px solid #DDE5EE;">
        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"><tr>
            <td width="42%" style="color:#737078;font-size:13px;text-align:right;">ادائیگی کا طریقہ</td>
            <td dir="ltr" style="color:#242328;font-size:14px;font-weight:700;text-align:left;overflow-wrap:anywhere;">{{ $paymentMethod }}</td>
        </tr></table>
    </td></tr>
    <tr><td style="padding:17px 12px;">
        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"><tr>
            <td width="50%" align="center" style="padding:0 8px;border-left:1px solid #DDE5EE;"><div style="margin:0 0 5px;color:#737078;font-size:12px;">آغاز</div><div dir="ltr" style="color:#242328;font-size:14px;font-weight:700;text-align:center;">{{ $userSubscription->start_date->format('Y-m-d') }}</div></td>
            <td width="50%" align="center" style="padding:0 8px;"><div style="margin:0 0 5px;color:#737078;font-size:12px;">اختتام</div><div dir="ltr" style="color:#242328;font-size:14px;font-weight:700;text-align:center;">{{ $userSubscription->end_date->format('Y-m-d') }}</div></td>
        </tr></table>
    </td></tr>
</table>
