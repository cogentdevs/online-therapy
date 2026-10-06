<!doctype html>
<html lang="ur" dir="rtl">

<body style="margin:0;background:#f5f7fa;font-family:Arial,Tahoma,sans-serif;color:#242328">
    <table role="presentation" width="100%">
        <tr>
            <td align="center" style="padding:30px 12px">
                <table role="presentation" width="600"
                    style="width:100%;max-width:600px;background:#fff;border-collapse:collapse">
                    @include('frontend.mails.partials.subscription-mail-header')

                    <tr>
                        <td style="padding:30px">
                            <h1 style="color:#315782;text-align:center">{{ $campaign->title }}</h1>

                            @if ($campaign->short_description)
                                <p style="line-height:2;white-space:pre-line">{{ $campaign->short_description }}</p>
                            @endif

                            @foreach ($campaign->contents as $item)
                                <table role="presentation" width="100%"
                                    style="margin:20px 0;border:1px solid #dde5ee">
                                    <tr>
                                        <td style="padding:20px">
                                            <h2 style="font-size:19px;color:#315782">{{ $item->displayTitle() }}</h2>
                                            <p style="line-height:1.9">{{ $item->truncatedDescription() }}</p>

                                            @if ($item->frontendUrl())
                                                <a href="{{ $item->frontendUrl() }}"
                                                    style="display:inline-block;background:#3f6ca1;color:#fff;padding:10px 18px;text-decoration:none;border-radius:5px">
                                                    مزید پڑھیں
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            @endforeach

                            <div style="text-align:center;color:#737078;font-size:12px">
                                @if ($isTest ?? false)
                                    اصل نیوز لیٹر میں ہر وصول کنندہ کے لیے محفوظ ان سبسکرائب لنک شامل ہوگا۔
                                @else
                                    <a href="{{ url('/newsletter/unsubscribe') }}/@{{ contact.DM_UNSUB_TOKEN }}"
                                        style="color:#315782">
                                        ڈیجیٹل میگزین سے ان سبسکرائب کریں
                                    </a>
                                    {{-- <span style="margin:0 7px">|</span>
                                    <a href="@{{ unsubscribe }}" style="color:#737078">
                                        Brevo ان سبسکرائب
                                    </a> --}}
                                @endif
                            </div>
                        </td>
                    </tr>

                    @include('frontend.mails.partials.subscription-mail-footer')
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
