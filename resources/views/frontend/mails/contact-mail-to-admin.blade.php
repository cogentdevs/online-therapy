<!DOCTYPE html>
<html lang="ur" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نیا رابطہ پیغام</title>
</head>

<body
    style="
        margin:0;
        padding:0;
        background-color:#F5F7FA;
        font-family:Arial,Helvetica,sans-serif;
        color:#242328;
        direction:rtl;
    ">

    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
        style="
            width:100%;
            margin:0;
            padding:0;
            background-color:#F5F7FA;
        ">

        <tr>
            <td align="center" style="padding:35px 15px;">

                {{-- Main Email Container --}}
                <table role="presentation" width="600" cellspacing="0" cellpadding="0" border="0"
                    style="
                        width:100%;
                        max-width:600px;
                        background-color:#ffffff;
                        border:1px solid #DDE5EE;
                        border-radius:10px;
                        overflow:hidden;
                    ">

                    {{-- Header --}}
                    <tr>
                        <td align="center"
                            style="
                                padding:28px 30px 24px;
                                background-color:#ffffff;
                                border-bottom:1px solid #DDE5EE;
                            ">

                            @if (!empty($generalSetting?->logo))
                                <img src="{{ asset($generalSetting->logo) }}"
                                    alt="{{ $generalSetting->app_name ?? config('app.name', 'Digital Magazine') }}"
                                    style="
                                        display:block;
                                        max-width:170px;
                                        max-height:70px;
                                        width:auto;
                                        height:auto;
                                        margin:0 auto 12px;
                                        border:0;
                                    ">
                            @endif

                            <div
                                style="
                                    color:#3F6CA1;
                                    font-size:18px;
                                    line-height:1.5;
                                    font-weight:700;
                                    text-align:center;
                                ">
                                {{ $generalSetting->app_name ?? config('app.name', 'Digital Magazine') }}
                            </div>

                        </td>
                    </tr>

                    {{-- Main Content --}}
                    <tr>
                        <td
                            style="
                                padding:35px 35px 30px;
                                text-align:right;
                                direction:rtl;
                            ">

                            {{-- Badge --}}
                            <div style="text-align:center;margin-bottom:15px;">
                                <span
                                    style="
                                        display:inline-block;
                                        padding:7px 16px;
                                        background-color:#EAF0F7;
                                        color:#3F6CA1;
                                        border-radius:20px;
                                        font-size:13px;
                                        line-height:1.5;
                                        font-weight:700;
                                    ">
                                    نیا رابطہ پیغام
                                </span>
                            </div>

                            {{-- Heading --}}
                            <h1
                                style="
                                    margin:0 0 8px;
                                    color:#17171A;
                                    font-size:25px;
                                    line-height:1.5;
                                    font-weight:700;
                                    text-align:center;
                                ">
                                نیا رابطہ پیغام موصول ہوا ہے
                            </h1>

                            {{-- Description --}}
                            <p
                                style="
                                    margin:0 0 30px;
                                    color:#737078;
                                    font-size:14px;
                                    line-height:1.8;
                                    text-align:center;
                                ">
                                ویب سائٹ کے رابطہ فارم کے ذریعے ایک نیا پیغام موصول ہوا ہے۔
                                پیغام کی مکمل تفصیلات درج ذیل ہیں۔
                            </p>

                            {{-- Contact Details --}}
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
                                style="
                                    width:100%;
                                    margin:0 0 25px;
                                    border:1px solid #DDE5EE;
                                    border-collapse:separate;
                                    border-spacing:0;
                                    direction:rtl;
                                ">

                                {{-- Section Heading --}}
                                <tr>
                                    <td colspan="2"
                                        style="
                                            padding:13px 18px;
                                            background-color:#3F6CA1;
                                            color:#ffffff;
                                            font-size:16px;
                                            line-height:1.5;
                                            font-weight:700;
                                            text-align:right;
                                        ">
                                        رابطہ کرنے والے کی تفصیلات
                                    </td>
                                </tr>

                                {{-- Name --}}
                                <tr>
                                    <td width="125"
                                        style="
                                            padding:14px 18px;
                                            background-color:#F5F7FA;
                                            border-bottom:1px solid #DDE5EE;
                                            color:#737078;
                                            font-size:14px;
                                            line-height:1.5;
                                            font-weight:600;
                                            text-align:right;
                                        ">
                                        نام
                                    </td>

                                    <td
                                        style="
                                            padding:14px 18px;
                                            border-bottom:1px solid #DDE5EE;
                                            color:#242328;
                                            font-size:14px;
                                            line-height:1.5;
                                            font-weight:700;
                                            text-align:right;
                                        ">
                                        {{ $contact->name }}
                                    </td>
                                </tr>

                                {{-- Email --}}
                                <tr>
                                    <td width="125"
                                        style="
                                            padding:14px 18px;
                                            background-color:#F5F7FA;
                                            border-bottom:1px solid #DDE5EE;
                                            color:#737078;
                                            font-size:14px;
                                            line-height:1.5;
                                            font-weight:600;
                                            text-align:right;
                                        ">
                                        ای میل
                                    </td>

                                    <td
                                        style="
                                            padding:14px 18px;
                                            border-bottom:1px solid #DDE5EE;
                                            color:#242328;
                                            font-size:14px;
                                            line-height:1.5;
                                            font-weight:700;
                                            word-break:break-word;
                                            text-align:right;
                                            direction:ltr;
                                        ">
                                        {{ $contact->email }}
                                    </td>
                                </tr>

                                {{-- Phone --}}
                                <tr>
                                    <td width="125"
                                        style="
                                            padding:14px 18px;
                                            background-color:#F5F7FA;
                                            border-bottom:1px solid #DDE5EE;
                                            color:#737078;
                                            font-size:14px;
                                            line-height:1.5;
                                            font-weight:600;
                                            text-align:right;
                                        ">
                                        فون نمبر
                                    </td>

                                    <td
                                        style="
                                            padding:14px 18px;
                                            border-bottom:1px solid #DDE5EE;
                                            color:#242328;
                                            font-size:14px;
                                            line-height:1.5;
                                            font-weight:700;
                                            text-align:right;
                                            direction:ltr;
                                        ">
                                        {{ $contact->phone }}
                                    </td>
                                </tr>

                                {{-- Subject --}}
                                <tr>
                                    <td width="125"
                                        style="
                                            padding:14px 18px;
                                            background-color:#F5F7FA;
                                            border-bottom:1px solid #DDE5EE;
                                            color:#737078;
                                            font-size:14px;
                                            line-height:1.5;
                                            font-weight:600;
                                            text-align:right;
                                        ">
                                        موضوع
                                    </td>

                                    <td
                                        style="
                                            padding:14px 18px;
                                            border-bottom:1px solid #DDE5EE;
                                            color:#242328;
                                            font-size:14px;
                                            line-height:1.7;
                                            font-weight:700;
                                            word-break:break-word;
                                            text-align:right;
                                        ">
                                        {{ $contact->subject }}
                                    </td>
                                </tr>

                                {{-- Submitted Date --}}
                                <tr>
                                    <td width="125"
                                        style="
                                            padding:14px 18px;
                                            background-color:#F5F7FA;
                                            color:#737078;
                                            font-size:14px;
                                            line-height:1.5;
                                            font-weight:600;
                                            text-align:right;
                                        ">
                                        موصول ہونے کی تاریخ
                                    </td>

                                    <td
                                        style="
                                            padding:14px 18px;
                                            color:#242328;
                                            font-size:14px;
                                            line-height:1.5;
                                            font-weight:700;
                                            text-align:right;
                                        ">
                                        {{ $contact->created_at?->format('d M Y, h:i A') }}
                                    </td>
                                </tr>

                            </table>

                            {{-- Message Section --}}
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
                                style="
                                    width:100%;
                                    margin:0 0 25px;
                                    border:1px solid #DDE5EE;
                                    border-collapse:separate;
                                    border-spacing:0;
                                    direction:rtl;
                                ">

                                <tr>
                                    <td
                                        style="
                                            padding:13px 18px;
                                            background-color:#3F6CA1;
                                            color:#ffffff;
                                            font-size:16px;
                                            line-height:1.5;
                                            font-weight:700;
                                            text-align:right;
                                        ">
                                        پیغام
                                    </td>
                                </tr>

                                <tr>
                                    <td
                                        style="
                                            padding:18px;
                                            background-color:#ffffff;
                                            color:#242328;
                                            font-size:14px;
                                            line-height:1.9;
                                            text-align:right;
                                            white-space:pre-wrap;
                                            word-break:break-word;
                                        ">
                                        {{ $contact->message }}</td>
                                </tr>

                            </table>

                            {{-- Information Box --}}
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
                                style="
                                    width:100%;
                                    margin:0;
                                    background-color:#EAF0F7;
                                    border-right:4px solid #3F6CA1;
                                ">

                                <tr>
                                    <td
                                        style="
                                            padding:16px 18px;
                                            text-align:right;
                                        ">

                                        <strong
                                            style="
                                                display:block;
                                                margin-bottom:5px;
                                                color:#315782;
                                                font-size:14px;
                                                line-height:1.5;
                                            ">
                                            اطلاع
                                        </strong>

                                        <span
                                            style="
                                                color:#242328;
                                                font-size:13px;
                                                line-height:1.8;
                                            ">
                                            یہ پیغام ویب سائٹ کے رابطہ فارم کے ذریعے موصول ہوا ہے۔
                                            مکمل پیغام اور اس کی حیثیت ایڈمن پینل میں بھی دیکھی جا سکتی ہے۔
                                        </span>

                                    </td>
                                </tr>

                            </table>

                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td align="center"
                            style="
                                padding:24px 30px;
                                background-color:#EAF0F7;
                                border-top:1px solid #DDE5EE;
                            ">

                            <div
                                style="
                                    margin-bottom:6px;
                                    color:#3F6CA1;
                                    font-size:16px;
                                    line-height:1.5;
                                    font-weight:700;
                                    text-align:center;
                                ">
                                {{ $generalSetting->app_name ?? config('app.name', 'Digital Magazine') }}
                            </div>

                            <div
                                style="
                                    margin-bottom:8px;
                                    color:#737078;
                                    font-size:12px;
                                    line-height:1.7;
                                    text-align:center;
                                ">
                                ڈیجیٹل میگزین انتظامیہ
                            </div>

                            <div
                                style="
                                    color:#737078;
                                    font-size:12px;
                                    line-height:1.7;
                                    text-align:center;
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
