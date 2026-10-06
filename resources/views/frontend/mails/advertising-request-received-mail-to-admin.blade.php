<!DOCTYPE html>
<html lang="ur" dir="rtl">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>نئی تشہیری درخواست</title></head>
<body style="margin:0;padding:0;width:100% !important;background:#F5F7FA;font-family:Arial,Tahoma,sans-serif;color:#242328;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="width:100%;background:#F5F7FA;">
        <tr><td align="center" style="padding:35px 15px;">
            <table role="presentation" width="600" cellspacing="0" cellpadding="0" border="0" align="center" style="width:100%;max-width:600px;margin:0 auto;background:#fff;border:1px solid #DDE5EE;border-collapse:separate;border-spacing:0;border-radius:10px;overflow:hidden;box-shadow:0 8px 24px rgba(49,87,130,.08);">
                @include('frontend.mails.partials.subscription-mail-header')
                <tr><td dir="rtl" style="padding:34px 35px 30px;background:#fff;direction:rtl;text-align:right;">
                    <h1 style="margin:0 0 12px;color:#17171A;font-size:25px;line-height:1.7;text-align:center;">نئی تشہیری درخواست موصول ہوئی ہے</h1>
                    <p style="margin:0 0 24px;color:#737078;font-size:14px;line-height:1.9;">یہ درخواست فی الحال زیرِ جائزہ ہے۔ مطلوبہ تشہیری مقامات ابھی محفوظ نہیں ہوئے۔</p>
                    @include('frontend.mails.partials.advertising-request-details', ['showContact' => true])
                </td></tr>
                @include('frontend.mails.partials.subscription-mail-footer')
            </table>
        </td></tr>
    </table>
</body>
</html>
