<!DOCTYPE html>
<html lang="en">
<!DOCTYPE html>
<html>
<head>
    <style>
        table, th, td {
            border: 1px solid black;
            border-collapse: collapse;
        }
        th, td {
            text-align: left;
            vertical-align: top;
            padding: 5px;
        }
        body {
            word-wrap: break-word;
            font-size: 10px;
        }
        .next-page {
            page-break-before: always;
        }
        header { position: fixed; top: 0px; left: 0px; width: 100%; height: 80px; margin-bottom: 10px; }
        footer { position: fixed; bottom: 0px; left: 0px; width: 100%; height: 100px; }
        * { font-family: DejaVu Sans, sans-serif; }
    </style>
</head>
<body style="padding-top:70px; padding-bottom: 100px;" onload="window.print()">
<header>
    <img src="{{ asset('theme/images/headerlogo.jpg') }}" height="65">
</header>
<footer>
    <img src="https://app.blueraynational.com/theme/images/footer.jpg" width="100%">
</footer>
<div class="maincon">
    <table width="100%">
        <tr>
            <td width="50%">Customer: {{ $quotation->customer->customer_name ?? '' }}</td>
            <td>QTN No: {{ $quotation->quotation_code ?? '' }}</td>
        </tr>
        <tr>
            <td>Address: {{ $quotation->customer->address ?? '' }}<br>
                @if(!empty($quotation->customer->postcode))Postal Code:{{ $quotation->customer->postcode }}<br>@endif
                @if(!empty($quotation->customer->city)), {{ $quotation->customer->city }}<br>@endif
                @if(!empty($quotation->customer->state)), {{ $quotation->customer->state }}<br>@endif
                @if(!empty($quotation->customer->country)), {{ $quotation->customer->country }}<br>@endif
            </td>
            <td>Date: {{ $quotation->quotation_date->format('d-m-Y') ?? '' }}</td>
        </tr>
        <tr>
            <td>Email: {{ $quotation->customer->email ?? '' }}</td>
            <td>Attn: {{ $quotation->customer->contact_person ?? '' }}</td>
        </tr>
        <tr>
            <td>GSM: {{ $quotation->customer->mobile ?? '' }}</td>
            <td>Designation: {{ $quotation->customer->designation ?? '' }}</td>
        </tr>
    </table>
    <br>
    <table width="100%">
        <tr>
            <td width="50%">Subject</td>
            <td>{{ $quotation->reference_no ?? '' }}</td>
        </tr>
    </table>
    <p>Dear <b>{{ $quotation->customer->customer_name ?? '' }}</b>,</p>
    <p><b>{{ $company_name ?? '' }}</b> is pleased to provide <b>{{ $quotation->customer->customer_name ?? '' }}</b> with the attached proposal with regard to the subject matter.</p>
    <p>The attached proposal outlines the solution we feel will best meet client’s objectives. We greatly appreciate your consideration of <b>{{ $company_name ?? '' }}</b> for this project. Our measure of success is how well we deliver solutions that help our customers meet their critical business objectives. We hope to have the opportunity to demonstrate this with you. We will be calling you to discuss our proposal and provide any additional information that may help your Evaluation. Until then, please contact us if you have any questions.</p>
    <p>We once again thank you for the opportunity given to serve you and looking forward to receive your valuable order to proceed further.</p>
    <p>Thanking you in advance</p>
    <h4>{{ $company_name ?? '' }}</h4>
    <h5>RHIYAS AHAMED,</h5>
    <p>
        Mobile: {{ $company_mobile ?? '' }} <br>
        Email: {{ $company_email ?? '' }}<br>
        blueray.national24@gmail.com <br>
        {{ $company_city ?? '' }} <br>
        {{ $company_country ?? '' }}
    </p>
    <h4 class="next-page mrg-top" align="center">Quotation</h4>
    <table width="100%">
        <tr>
            <td>Name: {{ $quotation->customer->customer_name ?? '' }}</td>
            <td>Date: {{ $quotation->quotation_date->format('d-m-Y') ?? '' }}</td>
        </tr>
        <tr>
            <td>Our Ref: {{ $quotation->quotation_code ?? '' }}</td>
            <td>Contact Name: RHIYAS</td>
        </tr>
        <tr>
            <td>Sub: {{ $quotation->reference_no ?? '' }}</td>
            <td>Contact No: {{ $company_mobile ?? '' }}</td>
        </tr>
    </table>
    <p>Dear <b>{{ $quotation->customer->customer_name ?? '' }}</b>, We quote to you the following </p>
    <table align="center" width="100%" height='100%'>
        <thead>
            <tr>
                <th>#</th>
                <th colspan="6">Description</th>
                <th colspan="3">Article No</th>
                <th colspan="3">Unit Cost</th>
                <th>Qty</th>
                <th colspan="3">Discount</th>
                <th colspan="3">Discount Amount</th>
                <th colspan="3">Taxable Value</th>
                <th colspan="3">Total Amount</th>
            </tr>
        </thead>
        <tbody>
            @php
                $i = 0;
                $tot_qty = 0;
                $tot_sales_price = 0;
                $tot_tax_amt = 0;
                $tot_discount_amt = 0;
                $tot_unit_total_cost = 0;
                $tot_total_cost = 0;
                $taxable_value_total = 0;
                $discount_total_amt = 0;
            @endphp
            @foreach($quotation->items as $item)
                @php
                    $description = $item->description ?? '';
                    $sku = $item->sku ?? '';
                    $unit_cost = $item->unit_cost ?? 0;
                    $qty = $item->qty ?? 0;
                    $discount = $item->discount_input ?? 0;
                    $discount_amt = $item->discount_amt ?? 0;
                    $taxable_value = $item->total_cost - $item->tax_amt;
                    $total_amount = $item->total_cost ?? 0;
                @endphp
                <tr>
                    <td>{{ ++$i }}</td>
                    <td colspan="6">{{ $description }}</td>
                    <td colspan="3">{{ $sku }}</td>
                    <td colspan="3">{{ number_format($unit_cost, 3) }}</td>
                    <td>{{ $qty }}</td>
                    <td colspan="3">{{ $discount }}%</td>
                    <td colspan="3">{{ number_format($discount_amt, 3) }}</td>
                    <td colspan="3" style="text-align: right;">{{ number_format($taxable_value, 3) }}</td>
                    <td colspan="3" style="text-align: right;">{{ number_format($total_amount, 3) }}</td>
                </tr>
                @php
                    $tot_qty += $qty;
                    $tot_sales_price += $unit_cost;
                    $tot_tax_amt += $item->tax_amt ?? 0;
                    $tot_discount_amt += $discount_amt;
                    $tot_unit_total_cost += $unit_cost;
                    $tot_total_cost += $total_amount;
                    $taxable_value_total += $taxable_value;
                    $discount_total_amt += $discount_amt;
                @endphp
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="13"></td>
                <td>{{ $tot_qty }}</td>
                <td colspan="3">-</td>
                <td colspan="3" style="text-align: right;"><b>{{ number_format($discount_total_amt, 3) }}</b></td>
                <td colspan="3" style="text-align: right;"><b>{{ number_format($taxable_value_total, 3) }}</b></td>
                <td colspan="3" style="text-align: right;"><b>{{ number_format($tot_total_cost, 3) }}</b></td>
            </tr>
            <tr>
                <td colspan="20" style="text-align: right;"><b>Total Excl Vat:</b></td>
                <td colspan="6" style="text-align: right;">{{ number_format($taxable_value_total, 3) }}</td>
            </tr>
            <tr>
                <td colspan="20" style="text-align: right;"><b>VAT @ 5% AMOUNT</b></td>
                <td colspan="6" style="text-align: right;">{{ number_format($tot_tax_amt, 3) }}</td>
            </tr>
            <tr>
                <td colspan="20" style="text-align: right;"><b>TOTAL INCL VAT</b></td>
                <td colspan="6" style="text-align: right;"><b>{{ number_format($tot_total_cost, 3) }}</b></td>
            </tr>
            <tr>
                <td colspan="26">
                    <span class="amt-in-word">Amount in words: <i style="font-weight:bold; text-transform: uppercase;">OMANI RIAL {{-- Add number to words logic here --}}</i></span>
                </td>
            </tr>
        </tfoot>
    </table>
    <div>
        <h4>Please Note:</h4>
        <p style="font-size:12px;">{{ $quotation->customer->customer_notes ?? '' }}</p>
    </div>
</div>
</body>
</html>
            vertical-align: top;
        }
        .details-right {
            text-align: right;
        }
        .info-group {
            margin-bottom: 8px;
        }
        .info-label {
            font-weight: bold;
            color: #333;
        }
        .info-value {
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table th {
            background-color: #333;
            color: white;
            padding: 10px;
            text-align: left;
            font-weight: bold;
        }
        table td {
            padding: 8px 10px;
            border-bottom: 1px solid #ddd;
        }
        table tr:hover {
            background-color: #f5f5f5;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .total-section {
            float: right;
            width: 40%;
            margin-top: 20px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #ddd;
        }
        .total-row.grand-total {
            font-weight: bold;
            font-size: 16px;
            border-top: 2px solid #333;
            border-bottom: 2px solid #333;
        }
        .footer {
            clear: both;
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            color: #666;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: bold;
        }
        .status-draft { background-color: #6c757d; color: white; }
        .status-submitted { background-color: #ffc107; color: black; }
        .status-approved { background-color: #28a745; color: white; }
        .status-rejected { background-color: #dc3545; color: white; }
        
        @media print {
            body {
                padding: 0;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; font-size: 14px; cursor: pointer;">
            🖨️ Print
        </button>
        <button onclick="window.close()" style="padding: 10px 20px; font-size: 14px; cursor: pointer; margin-left: 10px;">
            ❌ Close
        </button>
    </div>

    <div class="header">
        <h1>QUOTATION</h1>
        <p>{{ config('app.name', 'Your Company Name') }}</p>
        <p>Address Line 1, City, Country</p>
        <p>Phone: +968 XXXXXXXX | Email: info@company.com</p>
    </div>

    <div class="quotation-details">
        <div class="details-left">
            <div class="info-group">
                <span class="info-label">To:</span><br>
                <strong style="font-size: 14px;">{{ $quotation->customer->customer_name ?? 'N/A' }}</strong>
            </div>
            @if($quotation->customer->address)
            <div class="info-group">
                <span class="info-value">{{ $quotation->customer->address }}</span>
            </div>
            @endif
            @if($quotation->customer->phone)
            <div class="info-group">
                <span class="info-value">Phone: {{ $quotation->customer->phone }}</span>
            </div>
            @endif
            @if($quotation->customer->email)
            <div class="info-group">
                <span class="info-value">Email: {{ $quotation->customer->email }}</span>
            </div>
            @endif
        </div>
        
        <div class="details-right">
            <div class="info-group">
                <span class="info-label">Quotation No:</span><br>
                <span class="info-value">{{ $quotation->quotation_no }}</span>
            </div>
            <div class="info-group">
                <span class="info-label">Date:</span><br>
                <span class="info-value">{{ $quotation->quotation_date->format('d-m-Y') }}</span>
            </div>
            @if($quotation->reference_no)
            <div class="info-group">
                <span class="info-label">Reference:</span><br>
                <span class="info-value">{{ $quotation->reference_no }}</span>
            </div>
            @endif
            <div class="info-group">
                <span class="info-label">Status:</span><br>
                <span class="status-badge status-{{ strtolower($quotation->approval_status) }}">
                    {{ $quotation->approval_status }}
                </span>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%">#</th>
                <th style="width: 35%">Item Description</th>
                <th style="width: 15%" class="text-center">Quantity</th>
                <th style="width: 15%" class="text-right">Unit Price (OMR)</th>
                <th style="width: 15%" class="text-right">Discount (OMR)</th>
                <th style="width: 15%" class="text-right">Total (OMR)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($quotation->items as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>
                    <strong>{{ $item->item->item_name ?? 'N/A' }}</strong>
                    @if($item->description)
                    <br><small style="color: #666;">{{ $item->description }}</small>
                    @endif
                </td>
                <td class="text-center">{{ number_format($item->quantity, 2) }}</td>
                <td class="text-right">{{ number_format($item->unit_price, 3) }}</td>
                <td class="text-right">{{ number_format($item->discount ?? 0, 3) }}</td>
                <td class="text-right">{{ number_format($item->total_price, 3) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total-section">
        <div class="total-row">
            <span>Subtotal:</span>
            <span>{{ number_format($quotation->subtotal, 3) }} OMR</span>
        </div>
        @if($quotation->discount > 0)
        <div class="total-row">
            <span>Discount:</span>
            <span>- {{ number_format($quotation->discount, 3) }} OMR</span>
        </div>
        @endif
        @if($quotation->tax_amount > 0)
        <div class="total-row">
            <span>Tax:</span>
            <span>{{ number_format($quotation->tax_amount, 3) }} OMR</span>
        </div>
        @endif
        <div class="total-row grand-total">
            <span>Grand Total:</span>
            <span>{{ number_format($quotation->total_amount, 3) }} OMR</span>
        </div>
    </div>

    <div style="clear: both;"></div>

    @if($quotation->notes)
    <div style="margin-top: 30px;">
        <strong>Notes:</strong>
        <p style="margin-top: 10px; color: #666;">{{ $quotation->notes }}</p>
    </div>
    @endif

    <div class="footer">
        <p>Thank you for your business!</p>
        <p style="margin-top: 5px; font-size: 10px;">
            This is a computer-generated quotation and does not require a signature.
        </p>
        <p style="margin-top: 10px; font-size: 10px;">
            Generated on {{ now()->format('d-m-Y H:i:s') }} by {{ $quotation->creator->name ?? 'System' }}
        </p>
    </div>

    <script>
        // Auto-print on load (optional)
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>
