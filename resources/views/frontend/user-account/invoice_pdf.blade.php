<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Invoice</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 0;
        }

        html,
        body {
            width: 210mm;
            height: 297mm;
            margin: 0;
            padding: 0;
        }

        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        body {
            color: #172033;
            background: #fff;
            font-family: DejaVu Sans, sans-serif;
            font-size: 9pt;
        }

        .invoice-page {
            width: auto;
            margin: 0;
            padding: 11mm 10mm 0;
        }

        .layout-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .invoice-header td {
            vertical-align: middle;
        }

        .brand {
            width: 50%;
            text-align: left;
        }

        .brand-logo {
            display: block;
            width: auto;
            height: auto;
            max-width: 58mm;
            max-height: 27mm;
        }

        .brand-name {
            margin-top: 1.3mm;
            color: #184f84;
            font-size: 10.5pt;
        }

        .invoice-title {
            width: 50%;
            text-align: right;
            color: #164f84;
        }

        .invoice-title h1 {
            margin: 0;
            font-size: 32pt;
            line-height: 1;
            letter-spacing: .3pt;
            white-space: nowrap;
        }

        .invoice-title p {
            margin: 2mm 0 0;
            font-size: 13.5pt;
            white-space: nowrap;
        }

        .header-rule {
            margin: 5mm 0 8mm;
            border-top: .3mm solid #9eb7cf;
        }

        .cards-table {
            border-collapse: separate;
            border-spacing: 0;
        }

        .cards-table>tbody>tr>td {
            vertical-align: top;
        }

        .info-card {
            height: 47mm;
            padding: 4.5mm 5mm;
            background: #f1f7fc;
            border-radius: 2mm;
        }

        .info-card h2 {
            margin: 0 0 2.5mm;
            padding-bottom: 2.3mm;
            border-bottom: .25mm solid #bfd0df;
            color: #154f82;
            font-size: 15.5pt;
        }

        .info-row {
            width: 100%;
            border-collapse: collapse;
        }

        .info-row td {
            padding: 2mm 0;
            vertical-align: top;
            word-wrap: break-word;
        }

        .info-label {
            width: 38%;
            color: #315577;
        }

        .info-colon {
            width: 7%;
            color: #315577;
        }

        .info-value {
            width: 55%;
            color: #151c29;
        }

        .metadata-card {
            padding: 4mm 0;
        }

        .metadata-card .info-row td {
            padding: 2.25mm 4.5mm;
            border-bottom: .25mm solid #d5e2ed;
        }

        .metadata-card .info-row tr:last-child td {
            border-bottom: 0;
        }

        .purchase-table {
            width: 100%;
            margin-top: 8mm;
            border-collapse: collapse;
            border: .25mm solid #abc5dd;
            table-layout: fixed;
        }

        .purchase-table th {
            padding: 4mm 1.5mm;
            color: #fff;
            background: #356da4;
            border-right: .25mm solid #78a0c5;
            font-size: 10.5pt;
            text-align: center;
            white-space: normal;
        }

        .purchase-table th:last-child {
            border-right: 0;
        }

        .purchase-table td {
            height: 23mm;
            padding: 4mm 2.5mm;
            border-right: .25mm solid #c2d5e5;
            text-align: center;
            vertical-align: middle;
            word-wrap: break-word;
        }

        .purchase-table td:last-child {
            border-right: 0;
        }

        .purchase-table .description {
            text-align: left;
        }

        .purchase-table strong {
            display: block;
            margin-bottom: 1mm;
            font-size: 10.5pt;
        }

        .purchase-table small {
            color: #496784;
            font-size: 9pt;
        }

        .status-panel {
            margin-top: 8mm;
            padding: 7mm 10mm;
            background: #eefaf4;
            border-radius: 2mm;
        }

        .status-table {
            width: 100%;
            border-collapse: collapse;
        }

        .status-icon-cell {
            width: 21mm;
            vertical-align: middle;
        }

        .status-icon {
            width: 14mm;
            height: 14mm;
            border-radius: 7mm;
            color: #fff;
            background: #07904b;
            font-size: 27pt;
            font-weight: 700;
            line-height: 13mm;
            text-align: center;
        }

        .status-divider {
            width: .25mm;
            height: 16mm;
            background: #53b88b;
        }

        .status-copy {
            padding-left: 5.5mm;
            vertical-align: middle;
        }

        .status-copy h2 {
            margin: 0 0 2mm;
            color: #087d43;
            font-size: 15pt;
        }

        .status-copy p {
            margin: 0;
            font-size: 9.75pt;
        }

        .invoice-footer {
            position: fixed;
            right: 0;
            bottom: 0;
            left: 0;
            width: 210mm;
        }

        .footer-rule {
            border-top: .5mm solid #28639b;
        }

        .footer-details {
            width: 100%;
            background: #f1f8fd;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .footer-details>tbody>tr>td {
            width: 33.333%;
            height: 22mm;
            padding: 3mm 4mm;
            color: #164f84;
            vertical-align: middle;
            text-align: center;
            border-right: .25mm solid #abc2d7;
        }

        .footer-details td:last-child {
            border-right: 0;
        }

        .contact-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: auto;
        }

        .contact-icon-cell {
            width: 11mm;
            padding: 0 !important;
            border: 0 !important;
            vertical-align: middle !important;
        }

        .contact-value-cell {
            padding: 0 0 0 2mm !important;
            border: 0 !important;
            vertical-align: middle !important;
            text-align: left !important;
            word-wrap: break-word;
        }

        .pdf-icon {
            display: block;
            width: 9mm;
            height: 9mm;
            border: 0;
        }

        .social-link {
            display: inline-block;
            width: 9mm;
            height: 9mm;
            margin: 0 1mm;
            text-decoration: none;
        }

        .social-link .pdf-icon {
            width: 9mm;
            height: 9mm;
        }

        .copyright {
            min-height: 14mm;
            padding: 4mm 10mm 3mm;
            color: #fff;
            background: #245f97;
            font-size: 9pt;
            line-height: 1.3;
            text-align: center;
            word-wrap: break-word;
        }
    </style>
</head>

<body>
    @php
        $amount = (float) ($userSubscription->total ?? ($userSubscription->price ?? 0));
        $formattedAmount = number_format($amount, floor($amount) === $amount ? 0 : 2);
        $currency = $userSubscription->currency?->code ?: $userSubscription->currency?->symbol;
        $socials = [
            'youtube' => $generalSetting->youtube,
            'instagram' => $generalSetting->instagram,
            'facebook' => $generalSetting->facebook,
            'linkedin' => $generalSetting->linkedin,
            'x' => $generalSetting->x,
            'tiktok' => $generalSetting->tiktok,
        ];
    @endphp
    <main class="invoice-page">
        <table class="layout-table invoice-header">
            <tr>
                <td class="brand">
                    @if ($logoDataUri)
                        <img class="brand-logo" src="{{ $logoDataUri }}" alt="{{ $generalSetting->app_name }}">
                    @endif
                    @if ($generalSetting->app_name)
                        <div class="brand-name">{{ $generalSetting->app_name }}</div>
                    @endif
                </td>
                <td class="invoice-title">
                    <h1>INVOICE</h1>
                    {{-- <p>{{ $documentSubtitle }}</p> --}}
                </td>
            </tr>
        </table>
        <div class="header-rule"></div>

        <table class="layout-table cards-table">
            <tr>
                <td width="49%">
                    <section class="info-card">
                        <h2>Billed To</h2>
                        <table class="info-row">
                            <tr>
                                <td class="info-label">Name</td>
                                <td class="info-colon">:</td>
                                <td class="info-value">{{ $userSubscription->user?->name }}</td>
                            </tr>
                            <tr>
                                <td class="info-label">Email</td>
                                <td class="info-colon">:</td>
                                <td class="info-value">{{ $userSubscription->user?->email }}</td>
                            </tr>
                            <tr>
                                <td class="info-label">Contact Number</td>
                                <td class="info-colon">:</td>
                                <td class="info-value">{{ $userSubscription->user?->phone }}</td>
                            </tr>
                        </table>
                    </section>
                </td>
                <td class="gap" width="2%"></td>
                <td width="49%">
                    <section class="info-card metadata-card">
                        <table class="info-row">
                            <tr>
                                <td class="info-label">Invoice #</td>
                                <td class="info-colon">:</td>
                                <td class="info-value">{{ $userSubscription->invoice_no }}</td>
                            </tr>
                            <tr>
                                <td class="info-label">Invoice Date</td>
                                <td class="info-colon">:</td>
                                <td class="info-value">{{ ($userSubscription->reviewed_at ?? $userSubscription->created_at)?->format('d M Y') }}</td>
                            </tr>
                            <tr>
                                <td class="info-label">Transaction ID</td>
                                <td class="info-colon">:</td>
                                <td class="info-value">{{ $userSubscription->transaction_id }}</td>
                            </tr>
                            <tr>
                                <td class="info-label">Order Number</td>
                                <td class="info-colon">:</td>
                                <td class="info-value">{{ $userSubscription->order_no }}</td>
                            </tr>
                        </table>
                    </section>
                </td>
            </tr>
        </table>

        <table class="purchase-table">
            <colgroup>
                <col style="width: 22%;">
                <col style="width: 18%;">
                <col style="width: 17%;">
                <col style="width: 21.5%;">
                <col style="width: 21.5%;">
            </colgroup>
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Duration</th>
                    <th>Price</th>
                    <th>Purchase Date</th>
                    <th>Expiry Date</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="description">
                        <strong>{{ $userSubscription->product_name }}</strong><small>{{ $productType }}</small></td>
                    <td>{{ $duration }}</td>
                    <td>{{ trim(($currency ? $currency . ' ' : '') . $formattedAmount) }}</td>
                    <td>{{ $userSubscription->created_at?->format('d M Y') }}</td>
                    <td>{{ $userSubscription->end_date?->format('d M Y') }}</td>
                </tr>
            </tbody>
        </table>

        <section class="status-panel">
            <table class="status-table">
                <tr>
                    <td class="status-icon-cell">
                        <div class="status-icon">&#10003;</div>
                    </td>
                    <td style="width: 1px;">
                        <div class="status-divider"></div>
                    </td>
                    <td class="status-copy">
                        <h2>{{ $statusContent['heading'] }}</h2>
                        <p>{{ $statusContent['message'] }}</p>
                    </td>
                </tr>
            </table>
        </section>

        <footer class="invoice-footer">
            <div class="footer-rule"></div>
            <table class="footer-details">
                <tr>
                    <td>
                        <table class="contact-table">
                            <tr>
                                <td class="contact-icon-cell" width="18%"><img class="pdf-icon"
                                        src="{{ $pdfIcons['phone'] }}" alt=""></td>
                                <td class="contact-value-cell" width="82%">{{ $generalSetting->contact_1 }}</td>
                            </tr>
                        </table>
                    </td>
                    <td>
                        <table class="contact-table">
                            <tr>
                                <td class="contact-icon-cell" width="18%"><img class="pdf-icon"
                                        src="{{ $pdfIcons['email'] }}" alt=""></td>
                                <td class="contact-value-cell" width="82%">{{ $generalSetting->email }}</td>
                            </tr>
                        </table>
                    </td>
                    <td>
                        @foreach ($socials as $platform => $url)
                            @if (filled($url))
                                <a class="social-link" href="{{ $url }}"><img class="pdf-icon"
                                        src="{{ $pdfIcons[$platform] }}" alt=""></a>
                            @endif
                        @endforeach
                    </td>
                </tr>
            </table>
            <div class="copyright">{{ $generalSetting->app_name }} {{ date('Y') }} - All rights reserved.</div>
        </footer>
    </main>
</body>

</html>
