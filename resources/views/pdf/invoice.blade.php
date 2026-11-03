<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tax Invoice - {{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #333;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
        }
        .header-table, .meta-table, .items-table, .totals-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        .title {
            font-size: 18px;
            font-weight: bold;
            color: #1a365d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .badge {
            background-color: #2b6cb0;
            color: #fff;
            padding: 3px 8px;
            font-size: 9px;
            font-weight: bold;
            border-radius: 3px;
        }
        .meta-box {
            border: 1px solid #cbd5e0;
            padding: 8px;
            background-color: #f7fafc;
        }
        .items-table th {
            background-color: #edf2f7;
            color: #2d3748;
            border: 1px solid #cbd5e0;
            padding: 6px 4px;
            font-size: 10px;
            text-align: left;
        }
        .items-table td {
            border: 1px solid #e2e8f0;
            padding: 5px 4px;
            font-size: 10px;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .footer-note {
            font-size: 9px;
            color: #718096;
            border-top: 1px dashed #cbd5e0;
            padding-top: 8px;
            margin-top: 15px;
        }
        .signature-table {
            width: 100%;
            margin-top: 40px;
        }
        .signature-line {
            border-top: 1px solid #718096;
            width: 180px;
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
        }
    </style>
</head>
<body>

    <table class="header-table">
        <tr>
            <td style="width: 60%;">
                <div class="title">{{ \App\Services\SettingService::get('company_name', 'PharmaDistributors Nepal Pvt. Ltd.') }}</div>
                <div>{{ \App\Services\SettingService::get('company_address', 'Tripureshwor, Kathmandu, Nepal') }}</div>
                <div><strong>PAN / VAT:</strong> {{ \App\Services\SettingService::get('company_pan_vat', '601234567') }} | <strong>DDA Reg:</strong> {{ \App\Services\SettingService::get('company_dda_reg_no', 'DDA-D-10294/080') }}</div>
                <div><strong>Phone:</strong> {{ \App\Services\SettingService::get('company_phone', '+977-1-4412345') }} | <strong>Email:</strong> {{ \App\Services\SettingService::get('company_email', 'info@pharmadistributors.com.np') }}</div>
            </td>
            <td style="width: 40%; text-align: right; vertical-align: top;">
                <span class="badge">TAX INVOICE</span>
                <h3 style="margin: 5px 0 0 0; color: #2d3748;">{{ $invoice->invoice_number }}</h3>
                <div style="font-size: 10px; color: #4a5568;">Date (AD): <strong>{{ $invoice->invoice_date_ad?->format('Y-m-d') }}</strong></div>
                <div style="font-size: 10px; color: #4a5568;">Date (BS): <strong>{{ $invoice->invoice_date_bs }}</strong></div>
                <div style="font-size: 10px; color: #4a5568;">Payment: <strong>{{ strtoupper($invoice->payment_method) }}</strong></div>
            </td>
        </tr>
    </table>

    <table class="meta-table">
        <tr>
            <td class="meta-box" style="width: 50%; vertical-align: top;">
                <strong>BILLED TO:</strong><br>
                <span style="font-size: 12px; font-weight: bold; color: #2b6cb0;">
                    {{ $invoice->customer ? $invoice->customer->name : ($invoice->customer_name ?? 'Cash / Walk-in Customer') }}
                </span><br>
                @if($invoice->customer)
                    Address: {{ $invoice->customer->address ?? 'N/A' }}<br>
                    Phone: {{ $invoice->customer->phone ?? 'N/A' }}<br>
                    <strong>PAN/VAT:</strong> {{ $invoice->customer->pan_vat_number ?? ($invoice->customer_pan ?? 'N/A') }}<br>
                    <strong>DDA License:</strong> {{ $invoice->customer->dda_license_number ?? 'N/A' }}
                @else
                    <strong>PAN:</strong> {{ $invoice->customer_pan ?? 'N/A' }}
                @endif
            </td>
            <td class="meta-box" style="width: 50%; vertical-align: top;">
                <strong>DISPATCH LOCATION:</strong><br>
                Warehouse: <strong>{{ $invoice->warehouse?->name }}</strong> ({{ $invoice->warehouse?->code }})<br>
                Salesperson: {{ $invoice->salesperson?->name ?? 'Direct Office Sales' }}<br>
                Status: <strong>{{ strtoupper($invoice->status) }}</strong><br>
                Notes: {{ $invoice->notes ?? 'None' }}
            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th class="text-center" style="width: 4%;">#</th>
                <th style="width: 28%;">Product Description</th>
                <th style="width: 14%;">Batch No.</th>
                <th style="width: 10%;">Expiry</th>
                <th class="text-right" style="width: 8%;">Qty</th>
                <th class="text-center" style="width: 6%;">Unit</th>
                <th class="text-right" style="width: 10%;">Rate (NPR)</th>
                <th class="text-right" style="width: 8%;">Disc</th>
                <th class="text-right" style="width: 12%;">Total (NPR)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->lines as $idx => $line)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td>
                        <strong>{{ $line->product_name }}</strong><br>
                        <span style="font-size: 9px; color: #718096;">Code: {{ $line->product_code }}</span>
                    </td>
                    <td><strong>{{ $line->batch_number }}</strong></td>
                    <td>{{ $line->expiry_date_ad?->format('Y-m-d') }}</td>
                    <td class="text-right">{{ number_format($line->qty, 0) }}</td>
                    <td class="text-center">{{ $line->unit_name }}</td>
                    <td class="text-right">{{ number_format($line->unit_price, 2) }}</td>
                    <td class="text-right">{{ $line->discount_pct > 0 ? number_format($line->discount_pct, 1).'%' : '-' }}</td>
                    <td class="text-right font-bold">{{ number_format($line->line_total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table style="width: 100%;">
        <tr>
            <td style="width: 55%; vertical-align: top; padding-right: 15px;">
                <div style="font-size: 10px; margin-bottom: 8px;">
                    <strong>Tax Breakdown:</strong><br>
                    Non-Taxable / Exempt: <strong>NPR {{ number_format($invoice->non_taxable_amount, 2) }}</strong><br>
                    Taxable Amount (13%): <strong>NPR {{ number_format($invoice->taxable_amount, 2) }}</strong><br>
                    VAT Amount (13%): <strong>NPR {{ number_format($invoice->tax_amount, 2) }}</strong>
                </div>
                <div class="footer-note">
                    <strong>Terms & Conditions:</strong><br>
                    1. All pharmaceutical products supplied are certified manufactured in compliance with DDA GMP norms.<br>
                    2. In accordance with Nepal DDA regulations, cold-chain items (2°C - 8°C) cannot be returned once delivered.<br>
                    3. Interest @ 18% p.a. will be levied on invoices unpaid after credit maturity.
                </div>
            </td>
            <td style="width: 45%; vertical-align: top;">
                <table class="items-table" style="margin-bottom: 0;">
                    <tr>
                        <td style="width: 55%;">Gross Subtotal:</td>
                        <td class="text-right" style="width: 45%;">NPR {{ number_format($invoice->subtotal, 2) }}</td>
                    </tr>
                    @if($invoice->discount_amount > 0)
                        <tr>
                            <td>Total Discount:</td>
                            <td class="text-right" style="color: #c53030;">- NPR {{ number_format($invoice->discount_amount, 2) }}</td>
                        </tr>
                    @endif
                    <tr>
                        <td>VAT Amount:</td>
                        <td class="text-right">NPR {{ number_format($invoice->tax_amount, 2) }}</td>
                    </tr>
                    @if($invoice->delivery_charge > 0)
                        <tr>
                            <td>Delivery / Freight:</td>
                            <td class="text-right">NPR {{ number_format($invoice->delivery_charge, 2) }}</td>
                        </tr>
                    @endif
                    <tr style="background-color: #edf2f7; font-size: 12px;">
                        <td class="font-bold">Grand Total:</td>
                        <td class="text-right font-bold" style="color: #1a365d;">NPR {{ number_format($invoice->total_amount, 2) }}</td>
                    </tr>
                    @if($invoice->paid_amount > 0)
                        <tr>
                            <td>Paid / Cash:</td>
                            <td class="text-right">NPR {{ number_format($invoice->paid_amount, 2) }}</td>
                        </tr>
                    @endif
                    @if($invoice->credit_amount > 0)
                        <tr>
                            <td><strong>Credit (Due):</strong></td>
                            <td class="text-right font-bold" style="color: #c53030;">NPR {{ number_format($invoice->credit_amount, 2) }}</td>
                        </tr>
                    @endif
                </table>
            </td>
        </tr>
    </table>

    <table class="signature-table">
        <tr>
            <td style="width: 33%; text-align: center;">
                <div class="signature-line">Prepared By</div>
            </td>
            <td style="width: 33%; text-align: center;">
                <div class="signature-line">Checked By / Pharmacist</div>
            </td>
            <td style="width: 33%; text-align: center;">
                <div class="signature-line">Customer Signature & Stamp</div>
            </td>
        </tr>
    </table>

</body>
</html>
