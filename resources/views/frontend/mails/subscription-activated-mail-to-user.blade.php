<!DOCTYPE html>
<html lang="ur" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>سبسکرپشن فعال</title>
</head>

<body
    style="margin:0;padding:0;width:100% !important;background:#F5F7FA;font-family:Arial,Tahoma,sans-serif;color:#242328;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
        style="width:100%;background:#F5F7FA;">
        <tr>
            <td align="center" style="padding:35px 15px;">
                <table role="presentation" width="600" cellspacing="0" cellpadding="0" border="0" align="center"
                    style="width:100%;max-width:600px;margin:0 auto;background:#fff;border:1px solid #DDE5EE;border-collapse:separate;border-spacing:0;border-radius:10px;overflow:hidden;box-shadow:0 8px 24px rgba(49,87,130,.08);">
                    @include('frontend.mails.partials.subscription-mail-header')
                    <tr>
                        <td dir="rtl"
                            style="padding:34px 35px 30px;background:#fff;direction:rtl;text-align:right;">
                            @php($isFutureSubscription = $userSubscription->start_date?->isAfter(today()) === true)
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                <tr>
                                    <td align="center" style="padding:0 0 13px;"><span
                                            style="display:inline-block;padding:7px 16px;background:#EAF0F7;color:#3F6CA1;border-radius:20px;font-size:13px;font-weight:700;">{{ $isFutureSubscription ? 'تجدید طے ہو گئی' : 'سبسکرپشن فعال' }}</span>
                                    </td>
                                </tr>
                            </table>
                            <div
                                style="width:48px;height:48px;margin:0 auto 14px;background:#E3F5E9;border-radius:50%;color:#177245;font-size:27px;line-height:48px;font-weight:700;text-align:center;">
                                ✓</div>
                            <h1
                                style="margin:0 0 9px;color:#17171A;font-size:25px;line-height:1.7;font-weight:700;text-align:center;">
                                {{ $isFutureSubscription ? 'آپ کی سبسکرپشن کی تجدید کامیابی سے طے ہو گئی ہے' : 'آپ کی سبسکرپشن کامیابی سے فعال ہو گئی ہے' }}
                            </h1>
                            <p style="margin:0 0 25px;color:#737078;font-size:14px;line-height:1.9;text-align:center;">
                                {{ $isFutureSubscription ? 'نئی مدت مقررہ آغاز کی تاریخ سے شروع ہوگی اور موجودہ ادا شدہ مدت محفوظ رہے گی۔' : 'آپ اب اپنی منتخب سبسکرپشن میں شامل مواد سے فائدہ اٹھا سکتے ہیں۔' }}
                            </p>
                            <p style="margin:0 0 12px;font-size:15px;line-height:2;">السلام علیکم <strong
                                    style="color:#3F6CA1;">{{ $userSubscription->user->name }}</strong>،</p>
                            <p style="margin:0 0 24px;font-size:15px;line-height:2;">
                                {{ $isFutureSubscription ? 'آپ کی تجدید محفوظ کر دی گئی ہے۔ نئی مدت کی مکمل تفصیل درج ذیل ہے:' : 'آپ کی منتخب سبسکرپشن کامیابی سے فعال کر دی گئی ہے۔ خریداری کی مکمل تفصیل درج ذیل ہے:' }}
                            </p>
                            @php($productType = $userSubscription->product_for === 'plan' ? 'منصوبہ' : ($userSubscription->product_for === 'membership' ? 'رکنیت' : $userSubscription->product_for))
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
                                style="width:100%;margin:0 0 22px;background:#F8FAFC;border:1px solid #DDE5EE;border-collapse:separate;border-spacing:0;border-radius:8px;">
                                <tr>
                                    <td align="center" style="padding:21px 18px;">
                                        <div dir="auto"
                                            style="margin:0 0 11px;color:#17171A;font-size:21px;line-height:1.6;font-weight:700;overflow-wrap:anywhere;">
                                            {{ $userSubscription->product_name }}</div>
                                        <span
                                            style="display:inline-block;margin:0 3px;padding:5px 12px;background:#EAF0F7;color:#3F6CA1;border-radius:16px;font-size:12px;font-weight:700;">{{ $productType }}</span>
                                        <span
                                            style="display:inline-block;margin:0 3px;padding:5px 12px;background:#E3F5E9;color:#177245;border-radius:16px;font-size:12px;font-weight:700;">{{ $isFutureSubscription ? 'آنے والی' : 'فعال' }}</span>
                                    </td>
                                </tr>
                            </table>
                            @include('frontend.mails.partials.subscription-purchase-details')
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
                                style="width:100%;margin:22px 0 0;background:#EAF0F7;border-right:4px solid #3F6CA1;">
                                <tr>
                                    <td style="padding:16px 18px;color:#315782;font-size:13px;line-height:1.9;">اپنی
                                        سبسکرپشن سے متعلق کسی مدد کے لیے ہماری سپورٹ ٹیم سے رابطہ کریں۔</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    @include('frontend.mails.partials.subscription-mail-footer')
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
