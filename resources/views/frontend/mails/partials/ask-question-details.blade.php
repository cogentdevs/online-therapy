<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="width:100%;margin:0 0 24px;border:1px solid #DDE5EE;border-collapse:collapse;">
    <tr><td style="padding:12px;background:#EAF0F7;color:#315782;font-weight:700;">سوال نمبر</td><td dir="ltr" style="padding:12px;text-align:left;font-weight:700;color:#315782;">{{ $askQuestion->question_no }}</td></tr>
    <tr><td style="padding:12px;border-top:1px solid #DDE5EE;">نام</td><td style="padding:12px;border-top:1px solid #DDE5EE;">{{ $askQuestion->name }}</td></tr>
    @if ($showContact ?? false)
        <tr><td style="padding:12px;border-top:1px solid #DDE5EE;">ای میل</td><td dir="ltr" style="padding:12px;border-top:1px solid #DDE5EE;text-align:left;">{{ $askQuestion->email }}</td></tr>
        <tr><td style="padding:12px;border-top:1px solid #DDE5EE;">فون</td><td dir="ltr" style="padding:12px;border-top:1px solid #DDE5EE;text-align:left;">{{ $askQuestion->phone }}</td></tr>
    @endif
    <tr><td style="padding:12px;border-top:1px solid #DDE5EE;">عنوان</td><td style="padding:12px;border-top:1px solid #DDE5EE;">{{ $askQuestion->subject }}</td></tr>
    <tr><td style="padding:12px;border-top:1px solid #DDE5EE;">حالت</td><td style="padding:12px;border-top:1px solid #DDE5EE;">زیرِ جائزہ</td></tr>
    <tr><td style="padding:12px;border-top:1px solid #DDE5EE;">سوال</td><td style="padding:12px;border-top:1px solid #DDE5EE;white-space:pre-line;overflow-wrap:anywhere;">{{ $askQuestion->sawal }}</td></tr>
    @if ($showContact ?? false)
        <tr><td style="padding:12px;border-top:1px solid #DDE5EE;">موصول ہونے کا وقت</td><td style="padding:12px;border-top:1px solid #DDE5EE;">{{ $askQuestion->created_at->format('d M Y, h:i A') }}</td></tr>
    @endif
</table>
