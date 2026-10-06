<!DOCTYPE html>
<html lang="ur" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>نئی سبسکرپشن</title>
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
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                <tr>
                                    <td align="center" style="padding:0 0 14px;"><span
                                            style="display:inline-block;padding:7px 16px;background:#FFF2E6;color:#B95700;border-radius:20px;font-size:13px;font-weight:700;">نئی
                                            خریداری</span></td>
                                </tr>
                            </table>
                            <h1
                                style="margin:0 0 9px;color:#17171A;font-size:25px;line-height:1.7;font-weight:700;text-align:center;">
                                نئی سبسکرپشن خریدی گئی ہے</h1>
                            <p style="margin:0 0 26px;color:#737078;font-size:14px;line-height:1.9;text-align:center;">
                                ایک صارف نے کامیابی سے نئی سبسکرپشن خریدی اور فعال کی ہے۔</p>
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
                                style="width:100%;margin:0 0 22px;background:#EAF0F7;border:1px solid #DDE5EE;border-collapse:separate;border-spacing:0;border-radius:8px;">
                                <tr>
                                    <td colspan="2"
                                        style="padding:14px 18px;color:#315782;font-size:15px;font-weight:700;border-bottom:1px solid #DDE5EE;">
                                        صارف کی معلومات</td>
                                </tr>
                                <tr>
                                    <td width="35%"
                                        style="padding:12px 18px;color:#737078;font-size:13px;border-bottom:1px solid #DDE5EE;">
                                        نام</td>
                                    <td dir="auto"
                                        style="padding:12px 18px;font-size:14px;font-weight:700;border-bottom:1px solid #DDE5EE;overflow-wrap:anywhere;">
                                        {{ $userSubscription->user->name }}</td>
                                </tr>
                                <tr>
                                    <td
                                        style="padding:12px 18px;color:#737078;font-size:13px;border-bottom:1px solid #DDE5EE;">
                                        ای میل</td>
                                    <td dir="ltr"
                                        style="padding:12px 18px;font-size:14px;font-weight:700;text-align:left;word-break:break-word;">
                                        {{ $userSubscription->user->email }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:12px 18px;color:#737078;font-size:13px;">سبسکرپشن ID</td>
                                    <td dir="ltr"
                                        style="padding:12px 18px;font-size:14px;font-weight:700;text-align:left;">
                                        #{{ $userSubscription->id }}</td>
                                </tr>
                            </table>
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
                                            style="display:inline-block;margin:0 3px;padding:5px 12px;background:#E3F5E9;color:#177245;border-radius:16px;font-size:12px;font-weight:700;">فعال</span>
                                    </td>
                                </tr>
                            </table>
                            @include('frontend.mails.partials.subscription-purchase-details')
                        </td>
                    </tr>
                    @include('frontend.mails.partials.subscription-mail-footer')
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
