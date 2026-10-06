<!doctype html>
<html lang="ur" dir="rtl">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>دو مرحلہ توثیق</title>
</head>

<body style="margin:0;padding:0;background:#F5F7FA;font-family:Arial,Tahoma,sans-serif;color:#242328;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
        style="width:100%;background:#F5F7FA;">
        <tr>
            <td align="center" style="padding:35px 15px;">
                <table role="presentation" width="600" cellspacing="0" cellpadding="0" border="0"
                    style="width:100%;max-width:600px;background:#fff;border:1px solid #DDE5EE;">
                    <tr>
                        <td align="center" style="padding:28px 30px 24px;border-bottom:1px solid #DDE5EE;">
                            @if (!empty($generalSetting?->logo))
                                <img src="{{ asset($generalSetting->logo) }}"
                                    alt="{{ $generalSetting->app_name ?? config('app.name') }}"
                                    style="display:block;max-width:170px;max-height:70px;margin:0 auto 12px;">
                            @endif
                            <div style="color:#3F6CA1;font-size:18px;font-weight:700;direction:ltr;">
                                {{ $generalSetting->app_name ?? config('app.name', 'Digital Magazine') }}</div>
                        </td>
                    </tr>
                    <tr>
                        <td dir="rtl" style="padding:35px;text-align:right;line-height:2;">
                            <h1 style="margin:0 0 18px;color:#17171A;font-size:25px;text-align:center;">
                                {{ match ($purpose) {'login' => 'لاگ ان کی تصدیق','disable' => 'دو مرحلہ توثیق غیر فعال کریں','change_method' => 'نئے تصدیقی طریقے کی توثیق',default => 'دو مرحلہ توثیق فعال کریں'} }}
                            </h1>
                            <p style="margin:0 0 14px;">السلام علیکم <strong
                                    style="color:#3F6CA1;">{{ $user->name }}</strong>،</p>
                            <p style="margin:0 0 22px;">
                                {{ match ($purpose) {'login' => 'اپنا لاگ ان مکمل کرنے','disable' => 'دو مرحلہ توثیق غیر فعال کرنے','change_method' => 'نئے دو مرحلہ توثیقی طریقے کی تصدیق کرنے',default => 'دو مرحلہ توثیق فعال کرنے'} }}
                                کے لیے درج ذیل 6 ہندسوں کا تصدیقی کوڈ استعمال کریں:</p>
                            <div dir="ltr"
                                style="margin:0 auto 22px;padding:16px;background:#EAF0F7;border:1px solid #DDE5EE;color:#315782;font-size:32px;font-weight:700;letter-spacing:8px;text-align:center;">
                                {{ $otp }}</div>
                            <p style="margin:0 0 10px;color:#315782;">یہ کوڈ 10 منٹ تک قابلِ استعمال ہے۔</p>
                            <p style="margin:0;color:#737078;">اگر آپ نے یہ درخواست نہیں کی تو اس کوڈ کو کسی کے ساتھ
                                شیئر نہ کریں اور اس ای میل کو نظر انداز کریں۔</p>
                        </td>
                    </tr>
                    <tr>
                        <td align="center"
                            style="padding:24px 30px;background:#EAF0F7;border-top:1px solid #DDE5EE;color:#737078;font-size:12px;">
                            &copy; {{ date('Y') }}
                            {{ $generalSetting->app_name ?? config('app.name', 'Digital Magazine') }}۔ جملہ حقوق محفوظ
                            ہیں۔</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
