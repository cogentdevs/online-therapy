<!DOCTYPE html>
<html lang="ur" dir="rtl">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>{{ $statusLabel }}</title></head>
<body style="margin:0;padding:0;width:100% !important;background:#F5F7FA;font-family:Arial,Tahoma,sans-serif;color:#242328;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="width:100%;background:#F5F7FA;"><tr><td align="center" style="padding:35px 15px;">
        <table role="presentation" width="600" cellspacing="0" cellpadding="0" border="0" align="center" style="width:100%;max-width:600px;margin:0 auto;background:#fff;border:1px solid #DDE5EE;border-collapse:separate;border-spacing:0;border-radius:10px;overflow:hidden;">
            @include('frontend.mails.partials.subscription-mail-header')
            <tr><td dir="rtl" style="padding:34px 35px 30px;background:#fff;direction:rtl;text-align:right;">
                <h1 style="margin:0 0 12px;color:#17171A;font-size:25px;line-height:1.7;text-align:center;">{{ $statusLabel }}</h1>
                <p style="margin:0 0 16px;font-size:15px;line-height:2;">محترم {{ $adRequest->name }}،</p>
                <p style="margin:0 0 20px;color:#737078;font-size:14px;line-height:1.9;">
                    @if ($status === 'quote_sent') آپ کی درخواست پر پیشکش بھیج دی گئی ہے۔ تشہیری مقام ابھی محفوظ نہیں ہوا۔
                    @elseif ($status === 'confirmed') آپ کا منتخب تشہیری مقام تصدیق کے بعد محفوظ کر دیا گیا ہے۔
                    @elseif ($status === 'published') متعلقہ تشہیر شائع ہو گئی ہے۔ دیگر مقامات کی صورتحال الگ ہو سکتی ہے۔
                    @elseif ($status === 'rejected') آپ کی تشہیری درخواست مسترد کر دی گئی ہے۔
                    @elseif ($status === 'cancelled') آپ کی تشہیری درخواست منسوخ کر دی گئی ہے۔
                    @endif
                </p>
                @if ($placement)
                    <p style="margin:0 0 18px;color:#315782;font-size:14px;font-weight:700;">متعلقہ مقام: {{ $placement->displayLabel() }}</p>
                @endif
                <p style="margin:0 0 20px;color:#3F6CA1;font-size:18px;font-weight:700;">درخواست نمبر: {{ $adRequest->request_no }}</p>
                <p style="margin:0;color:#737078;font-size:14px;line-height:1.9;">مدت: {{ $adRequest->from_date->format('d M Y') }} تا {{ $adRequest->to_date->format('d M Y') }}</p>
                <p style="margin:12px 0 0;color:#737078;font-size:14px;line-height:1.9;">منتخب مقامات: @foreach ($adRequest->placements as $requestedPlacement){{ $requestedPlacement->displayLabel() }}@unless ($loop->last)، @endunless @endforeach</p>
            </td></tr>
            @include('frontend.mails.partials.subscription-mail-footer')
        </table>
    </td></tr></table>
</body>
</html>
