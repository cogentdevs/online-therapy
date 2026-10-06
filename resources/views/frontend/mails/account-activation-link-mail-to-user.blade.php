<!DOCTYPE html>
<html lang="ur">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>اپنا اکاؤنٹ ایکٹیویٹ کریں</title>
</head>

<body
    style="
        margin: 0;
        padding: 0;
        width: 100% !important;
        background-color: #F5F7FA;
        font-family: Arial, Tahoma, sans-serif;
        color: #242328;
    ">

    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
        style="
            width: 100%;
            margin: 0;
            padding: 0;
            background-color: #F5F7FA;
        ">

        <tr>
            <td align="center" style="padding: 35px 15px;">

                {{-- MAIN EMAIL CARD --}}
                <table role="presentation" width="600" cellspacing="0" cellpadding="0" border="0" align="center"
                    style="
                        width: 100%;
                        max-width: 600px;
                        margin: 0 auto;
                        background-color: #ffffff;
                        border: 1px solid #DDE5EE;
                        border-collapse: separate;
                        border-spacing: 0;
                    ">

                    {{-- HEADER --}}
                    <tr>
                        <td align="center"
                            style="
                                padding: 28px 30px 24px;
                                background-color: #ffffff;
                                border-bottom: 1px solid #DDE5EE;
                                text-align: center;
                            ">

                            @if (!empty($generalSetting?->logo))
                                <img src="{{ asset($generalSetting->logo) }}"
                                    alt="{{ $generalSetting->app_name ?? config('app.name', 'Digital Magazine') }}"
                                    style="
                                        display: block;
                                        width: auto;
                                        height: auto;
                                        max-width: 170px;
                                        max-height: 70px;
                                        margin: 0 auto 12px;
                                        border: 0;
                                    ">
                            @endif

                            <div
                                style="
                                    margin: 0;
                                    color: #3F6CA1;
                                    font-size: 18px;
                                    line-height: 1.5;
                                    font-weight: 700;
                                    text-align: center;
                                    direction: ltr;
                                ">
                                {{ $generalSetting->app_name ?? config('app.name', 'Digital Magazine') }}
                            </div>

                        </td>
                    </tr>


                    {{-- MAIN CONTENT --}}
                    <tr>
                        <td
                            style="
                                padding: 35px 35px 30px;
                                background-color: #ffffff;
                            ">

                            {{-- Badge --}}
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">

                                <tr>
                                    <td align="center"
                                        style="
                                            padding: 0 0 16px;
                                            text-align: center;
                                        ">

                                        <span dir="rtl"
                                            style="
                                                display: inline-block;
                                                padding: 7px 16px;
                                                background-color: #EAF0F7;
                                                color: #3F6CA1;
                                                border-radius: 20px;
                                                font-size: 13px;
                                                line-height: 1.5;
                                                font-weight: 700;
                                                direction: rtl;
                                            ">
                                            اکاؤنٹ ایکٹیویشن
                                        </span>

                                    </td>
                                </tr>

                            </table>


                            {{-- Heading --}}
                            <h1 dir="rtl"
                                style="
                                    margin: 0 0 8px;
                                    color: #17171A;
                                    font-size: 25px;
                                    line-height: 1.7;
                                    font-weight: 700;
                                    text-align: center;
                                    direction: rtl;
                                ">
                                اپنا اکاؤنٹ ایکٹیویٹ کریں
                            </h1>

                            <p dir="rtl"
                                style="
                                    margin: 0 0 30px;
                                    color: #737078;
                                    font-size: 14px;
                                    line-height: 1.9;
                                    text-align: center;
                                    direction: rtl;
                                ">
                                Digital Magazine پر اپنی رجسٹریشن مکمل کرنے کے لیے
                                اکاؤنٹ ایکٹیویشن درکار ہے۔
                            </p>


                            {{-- Greeting --}}
                            <div dir="rtl"
                                style="
                                    direction: rtl;
                                    text-align: right;
                                ">

                                <p
                                    style="
                                        margin: 0 0 14px;
                                        color: #242328;
                                        font-size: 15px;
                                        line-height: 2;
                                        text-align: right;
                                    ">
                                    السلام علیکم
                                    <strong style="color: #3F6CA1;">
                                        {{ $user->name }}
                                    </strong>،
                                </p>

                                <p
                                    style="
                                        margin: 0 0 25px;
                                        color: #242328;
                                        font-size: 15px;
                                        line-height: 2;
                                        text-align: right;
                                    ">
                                    Digital Magazine پر رجسٹریشن کرنے کا شکریہ۔
                                    اپنے اکاؤنٹ کو فعال کرنے کے لیے نیچے موجود
                                    <strong style="color: #315782;">
                                        اکاؤنٹ ایکٹیویٹ کریں
                                    </strong>
                                    بٹن پر کلک کریں۔
                                </p>

                            </div>


                            {{-- EXPIRY NOTICE --}}
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
                                style="
                                    width: 100%;
                                    margin: 0 0 28px;
                                    background-color: #EAF0F7;
                                    border-right: 4px solid #3F6CA1;
                                    border-collapse: separate;
                                    border-spacing: 0;
                                ">

                                <tr>
                                    <td dir="rtl"
                                        style="
                                            padding: 16px 18px;
                                            direction: rtl;
                                            text-align: right;
                                        ">

                                        <strong
                                            style="
                                                display: block;
                                                margin-bottom: 6px;
                                                color: #315782;
                                                font-size: 14px;
                                                line-height: 1.7;
                                                text-align: right;
                                            ">
                                            اہم اطلاع
                                        </strong>

                                        <span
                                            style="
                                                color: #242328;
                                                font-size: 14px;
                                                line-height: 1.9;
                                            ">
                                            یہ ایکٹیویشن لنک صرف
                                            <strong>24 گھنٹے</strong>
                                            تک قابلِ استعمال ہے۔ براہ کرم مقررہ وقت کے اندر
                                            اپنا اکاؤنٹ ایکٹیویٹ کر لیں۔
                                        </span>

                                    </td>
                                </tr>

                            </table>


                            {{-- ACTIVATION BUTTON --}}
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">

                                <tr>
                                    <td align="center"
                                        style="
                                            padding: 0 0 30px;
                                            text-align: center;
                                        ">

                                        <a href="{{ $activationUrl }}"
                                            style="
                                                display: inline-block;
                                                min-width: 180px;
                                                padding: 13px 30px;
                                                background-color: #3F6CA1;
                                                color: #ffffff !important;
                                                text-decoration: none;
                                                text-align: center;
                                                font-size: 15px;
                                                line-height: 1.5;
                                                font-weight: 700;
                                                border-radius: 6px;
                                            ">
                                            اکاؤنٹ ایکٹیویٹ کریں
                                        </a>

                                    </td>
                                </tr>

                            </table>


                            {{-- FALLBACK URL --}}
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
                                style="
                                    width: 100%;
                                    margin: 0 0 25px;
                                    background-color: #F5F7FA;
                                    border: 1px solid #DDE5EE;
                                    border-collapse: separate;
                                    border-spacing: 0;
                                ">

                                <tr>
                                    <td dir="rtl"
                                        style="
                                            padding: 15px 17px;
                                            direction: rtl;
                                            text-align: right;
                                        ">

                                        <p
                                            style="
                                                margin: 0 0 8px;
                                                color: #737078;
                                                font-size: 12px;
                                                line-height: 1.8;
                                                text-align: right;
                                            ">
                                            اگر اوپر موجود بٹن کام نہ کرے تو یہ لنک
                                            اپنے براؤزر میں کھولیں:
                                        </p>

                                        <p dir="ltr"
                                            style="
                                                margin: 0;
                                                direction: ltr;
                                                text-align: left;
                                                word-break: break-all;
                                                overflow-wrap: anywhere;
                                                color: #3F6CA1;
                                                font-size: 12px;
                                                line-height: 1.7;
                                            ">

                                            <a href="{{ $activationUrl }}"
                                                style="
                                                    color: #3F6CA1;
                                                    text-decoration: none;
                                                    word-break: break-all;
                                                    overflow-wrap: anywhere;
                                                ">
                                                {{ $activationUrl }}
                                            </a>

                                        </p>

                                    </td>
                                </tr>

                            </table>


                            {{-- SECURITY NOTICE --}}
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
                                style="
                                    width: 100%;
                                    border-top: 1px solid #DDE5EE;
                                ">

                                <tr>
                                    <td dir="rtl" align="center"
                                        style="
                                            padding-top: 20px;
                                            color: #737078;
                                            font-size: 13px;
                                            line-height: 1.9;
                                            text-align: center;
                                            direction: rtl;
                                        ">
                                        اگر آپ نے Digital Magazine پر اکاؤنٹ رجسٹر نہیں کیا،
                                        تو اس ای میل کو نظر انداز کر دیں۔
                                    </td>
                                </tr>

                            </table>

                        </td>
                    </tr>


                    {{-- FOOTER --}}
                    <tr>
                        <td align="center"
                            style="
                                padding: 24px 30px;
                                background-color: #EAF0F7;
                                border-top: 1px solid #DDE5EE;
                                text-align: center;
                            ">

                            <div
                                style="
                                    margin: 0 0 6px;
                                    color: #3F6CA1;
                                    font-size: 16px;
                                    line-height: 1.6;
                                    font-weight: 700;
                                    text-align: center;
                                    direction: ltr;
                                ">
                                {{ $generalSetting->app_name ?? config('app.name', 'Digital Magazine') }}
                            </div>

                            <div dir="rtl"
                                style="
                                    margin: 0 0 8px;
                                    color: #737078;
                                    font-size: 12px;
                                    line-height: 1.8;
                                    text-align: center;
                                    direction: rtl;
                                ">
                                معیاری میگزین، مضامین اور معلوماتی مواد
                            </div>

                            <div dir="rtl"
                                style="
                                    margin: 0;
                                    color: #737078;
                                    font-size: 12px;
                                    line-height: 1.8;
                                    text-align: center;
                                    direction: rtl;
                                ">
                                &copy; {{ date('Y') }}
                                {{ $generalSetting->app_name ?? config('app.name', 'Digital Magazine') }}۔
                                جملہ حقوق محفوظ ہیں۔
                            </div>

                        </td>
                    </tr>

                </table>

            </td>
        </tr>

    </table>

</body>

</html>
