<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Receipt — {{ $receipt->reference_no }}</title>
    <style>
        @page {
            margin: 40px 40px;
        }
        body { 
            font-family: 'Helvetica Neue', 'Helvetica', 'Arial', sans-serif; 
            font-size: 13px; 
            color: #333333; 
            line-height: 1.5; 
            margin: 0;
            padding: 0;
        }
        
        .page-container {
            padding: 0 20px;
        }

        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        
        /* HEADER */
        .header { margin-bottom: 30px; }
        .company-name { font-size: 26px; font-weight: bold; color: #111111; margin-bottom: 5px; }
        .company-details { font-size: 12px; color: #666666; line-height: 1.4; }
        
        .doc-title { font-size: 36px; font-weight: bold; color: #e5e7eb; text-align: right; letter-spacing: 2px; text-transform: uppercase; margin-bottom: 5px; }
        .doc-meta { text-align: right; font-size: 12px; color: #555555; line-height: 1.4; }

        /* INFO SECTION */
        .info-section { margin-bottom: 35px; }
        .info-section td { vertical-align: top; width: 50%; }
        
        .info-title { font-size: 10px; text-transform: uppercase; letter-spacing: 1.5px; color: #888888; border-bottom: 1px solid #eeeeee; padding-bottom: 5px; margin-bottom: 10px; font-weight: bold; }
        .info-content { font-size: 13px; color: #222222; font-weight: bold; margin-bottom: 4px; }
        .info-sub { font-size: 12px; color: #666666; }
        
        /* ITEMS TABLE */
        .items-heading { font-size: 14px; font-weight: bold; color: #111111; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 12px; border-bottom: 2px solid #111111; padding-bottom: 5px; }
        .items-table th { 
            color: #444444; 
            font-size: 10px; 
            text-transform: uppercase; 
            letter-spacing: 1px; 
            padding: 10px 5px; 
            text-align: left; 
            border-bottom: 1px solid #cccccc; 
        }
        .items-table td { 
            padding: 12px 5px; 
            border-bottom: 1px solid #eeeeee; 
            font-size: 12px; 
            color: #333333; 
        }
        .items-table .text-right { text-align: right; }
        .items-table .text-center { text-align: center; }
        
        .total-row td { 
            font-weight: bold; 
            color: #111111; 
            border-top: 2px solid #111111; 
            padding: 14px 5px; 
            font-size: 13px;
        }

        /* STATUS BADGE */
        .status { border: 1px solid #cccccc; padding: 2px 6px; border-radius: 3px; font-size: 10px; font-weight: bold; text-transform: uppercase; color: #555555; display: inline-block; margin-top: 4px; }

        /* FOOTER & SIGNATURES */
        .signatures { margin-top: 60px; width: 100%; table-layout: fixed; }
        .signatures td { vertical-align: bottom; text-align: center; width: 50%; padding: 0 40px; }
        .sig-line { border-bottom: 1px solid #111111; margin-bottom: 8px; }
        .sig-title { font-size: 10px; color: #666666; text-transform: uppercase; letter-spacing: 0.5px; }

        .footer { position: fixed; bottom: -10px; left: 0px; right: 0px; text-align: center; border-top: 1px solid #eeeeee; padding-top: 10px; }
        .footer p { font-size: 10px; color: #999999; }
    </style>
</head>
<body>
    @php
        $companyName = \App\Models\CompanySetting::getValue('company_name', 'CoreInventory IMS');
        $taxId = \App\Models\CompanySetting::getValue('tax_id', '');
        $address = \App\Models\CompanySetting::getValue('address', '');
        $phone = \App\Models\CompanySetting::getValue('phone', '');
        $email = \App\Models\CompanySetting::getValue('email', '');
    @endphp

    <div class="page-container">
        <!-- HEADER -->
        <table class="header">
            <tr>
                <td style="width: 50%;">
                    <div class="company-name">{{ $companyName }}</div>
                    <div class="company-details">
                        @if($address){{ $address }}<br>@endif
                        @if($phone)P: {{ $phone }}<br>@endif
                        @if($email)E: {{ $email }}<br>@endif
                        @if($taxId)Tax ID: {{ $taxId }}@endif
                    </div>
                </td>
                <td style="width: 50%; text-align: right;">
                    <div class="doc-title">RECEIPT</div>
                    <div class="doc-meta">
                        <strong style="color: #111; font-size: 14px;">{{ $receipt->reference_no }}</strong><br>
                        Date: {{ now()->format('M d, Y') }}<br>
                        Time: {{ now()->format('H:i A') }}
                    </div>
                </td>
            </tr>
        </table>

        <!-- INFO -->
        <table class="info-section">
            <tr>
                <td style="padding-right: 20px;">
                    <div class="info-title">Receipt Information</div>
                    <div class="info-content">Expected: {{ $receipt->expected_date ? \Carbon\Carbon::parse($receipt->expected_date)->format('M d, Y') : 'N/A' }}</div>
                    <div class="status">{{ $receipt->status }}</div>
                </td>
                <td style="padding-left: 20px;">
                    <div class="info-title">Supplier Details</div>
                    <div class="info-content">{{ $receipt->vendor_name ?? 'N/A' }}</div>
                    <div class="info-sub">Vendor supplied profile records.</div>
                </td>
            </tr>
        </table>

        <!-- ITEMS -->
        <div class="items-heading">Goods Received Items</div>
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 45%;">Item Description</th>
                    <th style="width: 25%;">SKU Code</th>
                    <th class="text-center" style="width: 10%;">Unit</th>
                    <th class="text-right" style="width: 15%;">Qty</th>
                </tr>
            </thead>
            <tbody>
                @php $totalQty = 0; @endphp
                @foreach($receipt->receiptItems as $i => $item)
                    @php $totalQty += $item->quantity; @endphp
                    <tr>
                        <td style="color: #888888;">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</td>
                        <td style="font-weight: bold;">{{ $item->product->name ?? 'N/A' }}</td>
                        <td style="font-family: monospace; color: #555555;">{{ $item->product->sku ?? '—' }}</td>
                        <td class="text-center">{{ $item->product->unit_of_measure ?? '—' }}</td>
                        <td class="text-right" style="font-weight: bold;">{{ number_format($item->quantity) }}</td>
                    </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="4" class="text-right" style="color: #666666; font-size: 10px; letter-spacing: 1px;">TOTAL QUANTITY RECEIVED</td>
                    <td class="text-right">{{ number_format($totalQty) }}</td>
                </tr>
            </tbody>
        </table>

        <!-- SIGNATURES -->
        <table class="signatures">
            <tr>
                <td>
                    <div class="sig-line"></div>
                    <div class="sig-title">Supplier Signature</div>
                </td>
                <td>
                    <div class="sig-line"></div>
                    <div class="sig-title">Authorized Receiver</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- FOOTER -->
    <div class="footer">
        <p>This is a computer-generated document. No signature is required for electronic verification.</p>
    </div>
</body>
</html>
