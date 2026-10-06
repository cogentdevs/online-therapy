<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>Payment Review Required</title></head>
<body style="margin:0;padding:0;background:#F5F7FA;font-family:Arial,Tahoma,sans-serif;color:#242328;">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"><tr><td align="center" style="padding:35px 15px;">
<table role="presentation" width="600" cellspacing="0" cellpadding="0" border="0" style="width:100%;max-width:600px;background:#fff;border:1px solid #DDE5EE;border-radius:10px;">
@include('frontend.mails.partials.subscription-mail-header')
<tr><td style="padding:34px 35px;">
<h1 style="margin:0 0 12px;color:#17171A;font-size:24px;text-align:center;">New Payment Requires Review</h1>
<p style="margin:0 0 24px;color:#737078;font-size:14px;line-height:1.8;text-align:center;">A subscription payment slip was submitted and remains pending until an administrator reviews it.</p>
<table role="presentation" width="100%" cellspacing="0" cellpadding="10" style="background:#F8FAFC;border:1px solid #DDE5EE;">
<tr><td>Subscription ID</td><td><strong>#{{ $userSubscription->id }}</strong></td></tr>
<tr><td>User</td><td>{{ $userSubscription->user->name }} ({{ $userSubscription->user->email }})</td></tr>
<tr><td>Plan</td><td>{{ $userSubscription->product_name }}</td></tr>
<tr><td>Amount</td><td>{{ $userSubscription->currency?->code ?: $userSubscription->currency?->symbol }} {{ number_format((float) $userSubscription->total, 2) }}</td></tr>
<tr><td>Submitted</td><td>{{ $userSubscription->payment_submitted_at?->format('d M Y, h:i A') }}</td></tr>
@if (filled($userSubscription->transaction_id))<tr><td>Reference</td><td>{{ $userSubscription->transaction_id }}</td></tr>@endif
</table>
</td></tr>
@include('frontend.mails.partials.subscription-mail-footer')
</table></td></tr></table>
</body></html>
