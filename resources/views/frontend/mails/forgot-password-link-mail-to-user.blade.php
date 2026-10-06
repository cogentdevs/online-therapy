<!doctype html>
<html lang="ur" dir="rtl">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>پاس ورڈ ری سیٹ کریں</title>
</head>

<body style="margin:0;padding:0;background:#F5F7FA;font-family:Arial,Tahoma,sans-serif;color:#242328;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
        style="background:#F5F7FA;width:100%;">
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
                            <h1 style="margin:0 0 18px;color:#17171A;font-size:25px;text-align:center;">پاس ورڈ ری سیٹ
                                کریں</h1>
                            <p style="margin:0 0 14px;">السلام علیکم <strong
                                    style="color:#3F6CA1;">{{ $user->name }}</strong>،</p>
                            <p style="margin:0 0 24px;">آپ کے اکاؤنٹ کا پاس ورڈ ری سیٹ کرنے کی درخواست موصول ہوئی ہے۔
                                نیا پاس ورڈ مقرر کرنے کے لیے نیچے موجود بٹن استعمال کریں۔</p>
                            <table role="presentation" width="100%">
                                <tr>
                                    <td align="center" style="padding:0 0 26px;"><a href="{{ $resetUrl }}"
                                            style="display:inline-block;padding:13px 30px;background:#3F6CA1;color:#fff;text-decoration:none;border-radius:6px;font-weight:700;">نیا
                                            پاس ورڈ سیٹ کریں</a></td>
                                </tr>
                            </table>
                            <div style="padding:15px 17px;background:#F5F7FA;border:1px solid #DDE5EE;font-size:12px;">
                                اگر بٹن کام نہ کرے تو یہ لنک کھولیں:<br><a dir="ltr" href="{{ $resetUrl }}"
                                    style="color:#3F6CA1;word-break:break-all;">{{ $resetUrl }}</a></div>
                            <p style="margin:20px 0 8px;color:#315782;">یہ لنک {{ $expiresInMinutes }} منٹ تک قابلِ
                                استعمال ہے۔</p>
                            <p style="margin:0;color:#737078;">اگر آپ نے پاس ورڈ ری سیٹ کی درخواست نہیں کی تو اس ای میل
                                کو نظر انداز کریں۔</p>
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
