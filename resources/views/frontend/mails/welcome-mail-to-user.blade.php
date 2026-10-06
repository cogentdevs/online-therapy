<!DOCTYPE html>
<html lang="ur">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Digital Magazine میں خوش آمدید</title>
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

                    {{-- Header --}}
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


                    {{-- Content --}}
                    <tr>
                        <td
                            style="
                                padding: 35px 35px 30px;
                                background-color: #ffffff;
                            ">

                            {{-- Badge --}}
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">

                                <tr>
                                    <td align="center" style="padding: 0 0 16px; text-align: center;">

                                        <span
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
                                            اکاؤنٹ کامیابی سے ایکٹیویٹ ہو گیا
                                        </span>

                                    </td>
                                </tr>

                            </table>


                            {{-- Heading --}}
                            <h1
                                style="
                                    margin: 0 0 8px;
                                    color: #17171A;
                                    font-size: 25px;
                                    line-height: 1.6;
                                    font-weight: 700;
                                    text-align: center;
                                    direction: rtl;
                                ">
                                Digital Magazine میں خوش آمدید
                            </h1>

                            <p
                                style="
                                    margin: 0 0 30px;
                                    color: #737078;
                                    font-size: 14px;
                                    line-height: 1.8;
                                    text-align: center;
                                    direction: rtl;
                                ">
                                آپ کا اکاؤنٹ کامیابی سے فعال ہو چکا ہے۔
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
                                    Digital Magazine میں خوش آمدید۔ آپ کا اکاؤنٹ کامیابی سے
                                    ایکٹیویٹ ہو گیا ہے۔ اب آپ اپنے اکاؤنٹ میں لاگ ان کر کے
                                    میگزین، مضامین اور دیگر دستیاب مواد تک رسائی حاصل کر سکتے ہیں۔
                                </p>

                            </div>


                            {{-- Status --}}
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
                                            اکاؤنٹ اسٹیٹس
                                        </strong>

                                        <span
                                            style="
                                                color: #242328;
                                                font-size: 14px;
                                                line-height: 1.9;
                                            ">
                                            آپ کا اکاؤنٹ فعال ہے اور اب لاگ ان کے لیے تیار ہے۔
                                        </span>

                                    </td>
                                </tr>

                            </table>


                            {{-- Login Button --}}
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">

                                <tr>
                                    <td align="center" style="padding: 0 0 30px; text-align: center;">

                                        <a href="{{ route('front.login') }}"
                                            style="
                                                display: inline-block;
                                                min-width: 150px;
                                                padding: 13px 30px;
                                                background-color: #3F6CA1;
                                                color: #ffffff !important;
                                                text-decoration: none;
                                                text-align: center;
                                                font-size: 15px;
                                                line-height: 1.5;
                                                font-weight: 700;
                                                border-radius: 6px;
                                                direction: rtl;
                                            ">
                                            لاگ ان کریں
                                        </a>

                                    </td>
                                </tr>

                            </table>


                            {{-- Help --}}
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
                                        اگر آپ کو اپنے اکاؤنٹ میں لاگ ان کرنے میں کسی مسئلے کا سامنا ہو،
                                        تو براہ کرم ہماری سپورٹ ٹیم سے رابطہ کریں۔
                                    </td>
                                </tr>

                            </table>

                        </td>
                    </tr>


                    {{-- Footer --}}
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
