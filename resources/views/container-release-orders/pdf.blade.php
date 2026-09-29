<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>CRO-{{ $cro->booking_no }}</title>
    <style>
        /* dompdf's default stylesheet sets `@page { margin: 1.2cm }`, which stacks on
           top of the body margin below. Overriding it here is the only way to control
           the real edge distance; 0.7cm (~7mm) stays inside the unprintable margin of
           virtually every consumer printer while reclaiming ~11mm per edge. */
        @page { margin: 0.7cm; }
        * { box-sizing: border-box; }
        body { font-family: DejaVu Sans, Helvetica, Arial, sans-serif; font-size: 11px; color: #1f2937; margin: 0; }
        .header { border-bottom: 3px solid #1f2937; padding-bottom: 8px; margin-bottom: 12px; }
        table.header-table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        table.header-table td { vertical-align: bottom; padding: 0; }
        td.header-brand { text-align: left; }
        td.header-mark { text-align: right; }
        .brand-logo { border: 0; }
        .company-name { font-size: 20px; font-weight: bold; color: #111827; text-transform: uppercase; }
        .company-contact { font-size: 9px; color: #6b7280; margin-top: 3px; }
        .doc-title { font-size: 13px; font-weight: bold; color: #374151; margin-top: 6px; }
        table.details { width: 100%; border-collapse: collapse; margin-top: 6px; }
        table.details th, table.details td { border: 1px solid #d1d5db; padding: 7px 9px; vertical-align: top; }
        table.details th { width: 22%; background: #f3f4f6; text-align: left; font-weight: bold; color: #111827; text-transform: uppercase; font-size: 9.5px; letter-spacing: 0.3px; }
        table.details td { width: 28%; }
        .section-label { margin-top: 18px; font-weight: bold; color: #111827; text-transform: uppercase; font-size: 10px; border-bottom: 1px solid #d1d5db; padding-bottom: 3px; }
        .notes { border: 1px solid #d1d5db; padding: 8px 10px; min-height: 44px; margin-top: 6px; }
        table.signatures { width: 100%; border-collapse: collapse; margin-top: 44px; }
        table.signatures td { width: 50%; padding: 0 12px; }
        .sign-box { border-top: 1px solid #6b7280; padding-top: 6px; }
        .sign-box p { margin: 2px 0; }
        .sign-box .role { font-weight: bold; color: #111827; }
        .footer { margin-top: 30px; font-size: 9px; color: #9ca3af; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <table class="header-table" cellspacing="0" cellpadding="0">
            <tr>
                <td class="header-brand" valign="bottom" width="40%">
                    <div class="company-name">{{ $brandName }}</div>
                    @if (! empty($brandContact))
                        <div class="company-contact">{{ implode(' | ', $brandContact) }}</div>
                    @endif
                </td>
                <td class="header-mark" valign="bottom" align="right" width="60%">
                    @if ($brandLogoPath && $brandLogoSize)
                        <img src="{{ $brandLogoPath }}" alt="" class="brand-logo" width="{{ $brandLogoSize['width'] }}" height="{{ $brandLogoSize['height'] }}">
                    @endif
                    <div class="doc-title">CRO-{{ $cro->booking_no }}</div>
                </td>
            </tr>
        </table>
    </div>

    <table class="details">
        <tr>
            <th>Booking No</th>
            <td>{{ $cro->booking_no ?? '—' }}</td>
            <th>Reference No</th>
            <td>{{ $cro->reference_no ?? '—' }}</td>
        </tr>
        <tr>
            <th>Booking Date</th>
            <td>{{ $cro->booking_date?->format('M j, Y') ?? '—' }}</td>
            <th>Cntr Owner</th>
            <td>{{ config("dropdowns.container_release_orders.cntr_owner.{$cro->cntr_owner}", '—') }}</td>
        </tr>
        <tr>
            <th>Commodity</th>
            <td>{{ $cro->commodity?->commodity_number ?? '—' }}</td>
            <th>DG Status</th>
            <td>{{ config("dropdowns.container_release_orders.dg_status.{$cro->dg_status}", '—') }}</td>
        </tr>
        <tr>
            <th>POL</th>
            <td>{{ $cro->pol ? $cro->pol->city.', '.$cro->pol->country : '—' }}</td>
            <th>POFD</th>
            <td>{{ $cro->pofd ? $cro->pofd->city.', '.$cro->pofd->country : '—' }}</td>
        </tr>
    </table>

    <div class="section-label">Notes</div>
    <div class="notes">{{ $cro->notes ?: '—' }}</div>

    <table class="signatures">
        <tr>
            <td>
                <div class="sign-box">
                    <p class="role">Prepared By</p>
                    <p>Name: _________________________</p>
                    <p>Signature: ____________________</p>
                    <p>Date: __________________________</p>
                </div>
            </td>
            <td>
                <div class="sign-box">
                    <p class="role">Received By</p>
                    <p>Name: _________________________</p>
                    <p>Signature: ____________________</p>
                    <p>Date: __________________________</p>
                </div>
            </td>
        </tr>
    </table>

    <div class="footer">Generated on {{ now()->format('M j, Y, H:i') }} by {{ auth()->user()?->name ?? 'System' }}</div>
</body>
</html>