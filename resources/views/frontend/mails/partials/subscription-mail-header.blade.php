<tr>
    <td align="center" style="padding:28px 30px 24px;background:#fff;border-bottom:1px solid #DDE5EE;text-align:center;">
        @if (!empty($generalSetting?->logo))
            <img src="{{ asset($generalSetting->logo) }}" alt="{{ $generalSetting->app_name ?? config('app.name', 'Digital Magazine') }}" style="display:block;width:auto;height:auto;max-width:170px;max-height:70px;margin:0 auto 12px;border:0;">
        @endif
        <div dir="ltr" style="color:#3F6CA1;font-size:18px;line-height:1.5;font-weight:700;text-align:center;">{{ $generalSetting->app_name ?? config('app.name', 'Digital Magazine') }}</div>
    </td>
</tr>
