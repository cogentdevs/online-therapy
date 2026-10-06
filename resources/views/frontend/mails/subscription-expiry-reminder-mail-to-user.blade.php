<!DOCTYPE html>
<html lang="ur" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>سبسکرپشن کی میعاد ختم ہونے کی یاد دہانی</title>
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
                                    <td align="center" style="padding:0 0 14px;">
                                        <span
                                            style="display:inline-block;padding:7px 16px;background:#FFF2E6;color:#B95700;border-radius:20px;font-size:13px;font-weight:700;">میعاد
                                            کی یاد دہانی</span>
                                    </td>
                                </tr>
                            </table>

                            <div
                                style="width:48px;height:48px;margin:0 auto 14px;background:#FFF2E6;border-radius:50%;color:#F07C10;font-size:25px;line-height:48px;font-weight:700;text-align:center;">
                                !</div>
                            <h1
                                style="margin:0 0 9px;color:#17171A;font-size:25px;line-height:1.7;font-weight:700;text-align:center;">
                                آپ کی سبسکرپشن جلد ختم ہونے والی ہے</h1>
                            <p style="margin:0 0 24px;color:#737078;font-size:14px;line-height:1.9;text-align:center;">
                                آپ کی سبسکرپشن <strong style="color:#B95700;">{{ $reminderDays }} دن</strong> بعد ختم
                                ہونے والی ہے۔
                            </p>

                            <p style="margin:0 0 12px;font-size:15px;line-height:2;">السلام علیکم <strong
                                    style="color:#3F6CA1;">{{ $recipientName }}</strong>،</p>
                            <p style="margin:0 0 22px;font-size:15px;line-height:2;">بلا تعطل رسائی جاری رکھنے کے لیے
                                اپنی موجودہ سبسکرپشن کی تفصیل دیکھیں اور ضرورت کے مطابق نئی سبسکرپشن منتخب کریں۔</p>

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
                                style="width:100%;margin:0 0 22px;background:#F8FAFC;border:1px solid #DDE5EE;border-collapse:separate;border-spacing:0;border-radius:8px;">
                                <tr>
                                    <td align="center" style="padding:21px 18px;border-bottom:1px solid #DDE5EE;">
                                        <div dir="auto"
                                            style="margin:0 0 11px;color:#17171A;font-size:21px;line-height:1.6;font-weight:700;overflow-wrap:anywhere;">
                                            {{ $productName }}</div>
                                        <span
                                            style="display:inline-block;padding:5px 12px;background:#EAF0F7;color:#3F6CA1;border-radius:16px;font-size:12px;font-weight:700;">{{ $productType }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:16px 18px;border-bottom:1px solid #DDE5EE;">
                                        <div style="margin:0 0 9px;color:#737078;font-size:12px;">شامل ماڈیولز</div>
                                        <div style="direction:rtl;text-align:right;line-height:2.3;">
                                            @forelse ($moduleLabels as $moduleLabel)
                                                <span
                                                    style="display:inline-block;margin:2px 0 2px 5px;padding:4px 10px;background:#EAF0F7;color:#315782;border-radius:14px;font-size:12px;font-weight:700;">{{ $moduleLabel }}</span>
                                            @empty
                                                <span style="color:#737078;font-size:13px;">کوئی ماڈیول درج نہیں</span>
                                            @endforelse
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center" style="padding:18px;">
                                        <div style="margin:0 0 5px;color:#737078;font-size:12px;">اختتامی تاریخ</div>
                                        <div dir="ltr"
                                            style="color:#242328;font-size:17px;line-height:1.5;font-weight:700;text-align:center;">
                                            {{ $expiryDate }}</div>
                                    </td>
                                </tr>
                            </table>

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                <tr>
                                    <td align="center" style="padding:2px 0 4px;">
                                        <a href="{{ $renewalUrl }}"
                                            style="display:inline-block;padding:13px 25px;background:#3F6CA1;color:#fff;border-radius:6px;font-size:15px;font-weight:700;text-decoration:none;">سبسکرپشن
                                            کی تجدید کریں</a>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:18px 0 0;color:#737078;font-size:12px;line-height:1.8;text-align:center;">
                                تجدید خودکار نہیں ہے۔ جاری رسائی کے لیے اپنی پسند کی سبسکرپشن دستی طور پر منتخب کریں۔
                            </p>
                        </td>
                    </tr>

                    @include('frontend.mails.partials.subscription-mail-footer')
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
