<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Sales Invoice {{ $invoice->invoice_number }}</title>
    <style>
        @page { margin: 0.7cm; }
        body { margin: 0; color: #17212a; font-family: DejaVu Sans, sans-serif; font-size: 10pt; }
        h1, h2, p { margin: 0; }
        h1 { color: #111f88; font-size: 23pt; line-height: 1.05; }
        .muted { color: #6b7280; }
        .blue { color: #111f88; }
        .header { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
        .header td { width: 50%; vertical-align: top; }
        .logo { text-align: right; }
        .logo img { object-fit: contain; }
        .contact { color: #111f88; text-align: right; line-height: 1.55; margin-top: 10px; }
        .seller-name { font-weight: bold; margin: 12px 0 5px; }
        .seller-info { color: #6b7280; white-space: pre-line; line-height: 1.45; }
        .addresses { width: 100%; border-collapse: collapse; margin: 14px 0 22px; }
        .addresses td { width: 50%; vertical-align: top; padding: 0 12px 0 0; }
        .section-title { background: #7d7d7d; color: white; font-weight: bold; padding: 5px 8px; margin: 14px 0 8px; }
        .info-row { margin: 8px 0; }
        .meta { width: 100%; border-collapse: collapse; }
        .meta td { padding: 4px 6px; }
        .meta td:first-child { background: #818181; color: white; width: 45%; font-weight: bold; }
        .meta td:last-child { color: #111f88; }
        .lines { width: 100%; border-collapse: collapse; margin-top: 18px; }
        .lines th { background: #718293; color: white; padding: 7px; text-align: left; }
        .lines th:last-child, .lines td.amount { text-align: right; white-space: nowrap; }
        .lines td { padding: 8px 7px; vertical-align: top; }
        .container { color: #111f88; font-weight: bold; padding-top: 6px; }
        .totals { width: 48%; margin: 22px 0 0 auto; border-collapse: collapse; }
        .totals td { padding: 5px 8px; text-align: right; color: #111f88; }
        .totals tr:last-child td { font-size: 12pt; font-weight: bold; }
        .payment-page { page-break-before: always; color: #111f88; font-weight: bold; line-height: 1.7; }
        .payment-page h2 { font-size: 12pt; margin-bottom: 4px; }
        .instructions { white-space: pre-line; margin-top: 42px; }
        .signature { margin: 4px 0 48px 11%; }
        .footer { position: fixed; bottom: -5px; right: 0; color: #777; font-size: 8pt; }
    </style>
</head>
<body>
    <table class="header">
        <tr>
            <td>
                <h1>Sales<br>Invoice</h1>
                <div class="seller-name">{{ $company->displayName() }}{{ filled($company->subtitle) ? ' '.$company->subtitle : '' }}</div>
                @if (filled($company->billing_address))<div class="seller-info">{{ $company->billing_address }}</div>@endif
            </td>
            <td class="logo">
                @if ($brandLogoPath && $brandLogoSize && is_file($brandLogoPath))
                    <img src="{{ $brandLogoPath }}" alt="Company logo" width="{{ $brandLogoSize['width'] }}" height="{{ $brandLogoSize['height'] }}">
                @endif
                <div class="contact">
                    @if (filled($company->number))<div>Tel: {{ $company->number }}</div>@endif
                    @if (filled($company->primaryEmail()))<div>Email: {{ $company->primaryEmail() }}</div>@endif
                    @if (filled($company->website))<div>{{ $company->website }}</div>@endif
                </div>
            </td>
        </tr>
    </table>

    <table class="addresses">
        <tr>
            <td>
                <div class="section-title">Invoice to:</div>
                <div class="info-row"><strong>{{ $invoice->party?->name }}</strong></div>
                @if (filled($invoice->party?->address))<div class="info-row">{{ $invoice->party->address }}</div>@endif
                @if (filled($invoice->customer_contact))<div class="info-row"><strong>Contact:</strong> {{ $invoice->customer_contact }}</div>@endif
                @if (filled($invoice->party?->email))<div class="info-row">{{ $invoice->party->email }}</div>@endif
            </td>
            <td>
                <table class="meta">
                    <tr><td>Sales Invoice</td><td>{{ $invoice->invoice_number }}</td></tr>
                    <tr><td>Date</td><td>{{ $invoice->invoice_date?->format('d/m/Y') ?? '' }}</td></tr>
                    <tr><td>Due Date</td><td>{{ $invoice->due_date?->format('d/m/Y') ?? '' }}</td></tr>
                    <tr><td>Our Ref</td><td>{{ $invoice->our_reference }}</td></tr>
                    <tr><td>Created Date</td><td>{{ $invoice->created_at?->format('d/m/Y') ?? '' }}</td></tr>
                </table>
            </td>
        </tr>
    </table>

    @if (filled($invoice->customer_contact) || filled($invoice->remarks))
        <div class="section-title">Invoice Contact / Remarks</div>
        @if (filled($invoice->customer_contact))<div class="info-row"><strong>Contact</strong> &nbsp; {{ $invoice->customer_contact }}</div>@endif
        @if (filled($invoice->remarks))<div class="info-row"><strong>Remarks</strong> &nbsp; {{ $invoice->remarks }}</div>@endif
    @endif

    <table class="lines">
        <thead><tr><th>Invoice details:</th><th>Amount ({{ $invoice->currency_code }})</th></tr></thead>
        <tbody>
        @foreach ($invoice->details as $detail)
            <tr>
                <td>{{ $detail->description }}
                    @if (filled($detail->container_number))<div class="container">Container Number: {{ $detail->container_number }}</div>@endif
                </td>
                <td class="amount">{{ number_format((float) $detail->amount, 2) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <table class="totals">
        <tr><td>Net</td><td>{{ number_format((float) $invoice->subtotal, 2) }}</td></tr>
        <tr><td>VAT @ {{ number_format((float) $invoice->vat_rate, 2) }}%</td><td>{{ number_format((float) $invoice->vat_amount, 2) }}</td></tr>
        <tr><td>Total {{ $invoice->currency_code }}</td><td>{{ number_format((float) $invoice->total_amount, 2) }}</td></tr>
    </table>

    <div class="footer">{{ $invoice->invoice_number }}</div>

    <div class="payment-page">
        <h2>Payment instructions</h2>
        <div>Currency: {{ $invoice->currency_code }}</div>
        @if (filled($bankAccount->bank_name) || filled($bankAccount->bank))<div>PLEASE REMIT TO: Bank name: {{ $bankAccount->bank_name ?: $bankAccount->bank }}</div>@endif
        @if (filled($bankAccount->address))<div>Address: {{ $bankAccount->address }}</div>@endif
        @if (filled($bankAccount->iban))<div>IBAN: {{ $bankAccount->iban }}</div>@endif
        @if (filled($bankAccount->account))<div>Account: {{ $bankAccount->account }}</div>@endif
        @if (filled($bankAccount->swift))<div>SWIFT: {{ $bankAccount->swift }}</div>@endif
        @if (filled($bankAccount->beneficiary_name))<div>Beneficiary: {{ $bankAccount->beneficiary_name }}</div>@endif
        @if (filled($company->invoice_payment_instructions))
            <div class="instructions">{{ $company->invoice_payment_instructions }}</div>
        @endif
        <div class="signature">On behalf of {{ $company->displayName() }}</div>
        <div>This is a computer generated invoice. No signature is required.</div>
    </div>
</body>
</html>
