<!DOCTYPE html>
<html>
<head>
    <style>
        table {
            border: 1px solid black;
            border-collapse: collapse;
            width: 100%;
            font-family: 'Open Sans', 'Martel Sans', sans-serif;
            font-size:11px;
        }
        th, td {
            padding: 2px 5px;
            text-align: left;
            vertical-align:top;
            border: 1px solid black;
        }
        body{
          word-wrap: break-word;
          padding:0 20mm;
        }
        .mrg-top{
            margin-top:140px;
        }
        @page { 
            margin: 0px 0px; 
            size: A4;
        }
        header {  Position:fixed; top:0px; left: 0px; width:100%; }
        footer {  Position:fixed; bottom: 0px; left: 0px; width:100%; }
        .next-page{
            page-break-before: always;
        }
    </style>
</head>
<body onload="window.print();" class="mrg-top">
<header><img src="https://app.blueraynational.com/theme/images/header.jpg" width="100%" height="auto"></header>
<footer><img src="https://app.blueraynational.com/theme/images/footer.jpg" width="100%" height="auto"></footer>
<div class="maincon">
    <table>
        <tr>
            <td width="60%">Customer: {{ $deliveryNote->customer->customer_name ?? '' }}</td>
            <td>DC No: {{ $deliveryNote->delivery_code ?? '' }}</td>
        </tr>
        <tr>
            <td>Address: {{ $deliveryNote->customer->address ?? '' }}<br>
                @if(!empty($deliveryNote->customer->postcode))Postal Code:{{ $deliveryNote->customer->postcode }}<br>@endif
                @if(!empty($deliveryNote->customer->city)), {{ $deliveryNote->customer->city }}<br>@endif
                @if(!empty($deliveryNote->customer->state)), {{ $deliveryNote->customer->state }}<br>@endif
                @if(!empty($deliveryNote->customer->country)), {{ $deliveryNote->customer->country }}<br>@endif
            </td>
            <td>Date: {{ $deliveryNote->delivery_date->format('d-m-Y') ?? '' }}</td>
        </tr>
        <tr>
            <td>Email: {{ $deliveryNote->customer->email ?? '' }}</td>
            <td>Attn: {{ $deliveryNote->customer->contact_person ?? '' }}</td>
        </tr>
        <tr>
            <td>GSM: {{ $deliveryNote->customer->mobile ?? '' }}</td>
            <td>Designation: {{ $deliveryNote->customer->designation ?? '' }}</td>
        </tr>
    </table>
    <br>
    <table width="100%">
        <tr>
            <td>Subject</td>
            <td>{{ $deliveryNote->reference_no ?? '' }}</td>
        </tr>
    </table>
    <p>Dear <b>{{ $deliveryNote->customer->customer_name ?? '' }}</b>,</p>
    <p><b>{{ $company_name ?? '' }}</b> is pleased to provide <b>{{ $deliveryNote->customer->customer_name ?? '' }}</b> with the attached delivery note with regard to the subject matter.</p>
    <p>The attached document outlines the items delivered. Please review and confirm receipt. If you have any questions, please contact us.</p>
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
    <h4 class="next-page mrg-top" align="center">Delivery Note</h4>
    <table align="center" width="100%" height='100%'>
        <thead>
            <tr>
                <th>#</th>
                <th colspan="3">Description</th>
                <th>Article No</th>
                <th>Unit Cost</th>
                <th>Quantity</th>
                <th>Taxable Value</th>
                <th>Tax</th>
                <th>Tax Amount</th>
                <th>Total Amount</th>
            </tr>
        </thead>
        <tbody>
            @php
                $i=0;
                $tot_qty=0;
                $tot_sales_price=0;
                $tot_tax_amt=0;
                $tot_discount_amt=0;
                $tot_unit_total_cost=0;
                $tot_total_cost=0;
                $taxable_value_total = 0;
            @endphp
            @foreach($deliveryNote->items as $item)
                @php
                    $description = $item->description ?? '';
                    $sku = $item->sku ?? '';
                    $unit_cost = $item->unit_cost ?? 0;
                    $qty = $item->qty ?? 0;
                    $taxable_value = $item->total_cost - $item->tax_amt;
                    $tax_name = $item->tax_name ?? '';
                    $tax_amt = $item->tax_amt ?? 0;
                    $total_amount = $item->total_cost ?? 0;
                @endphp
                <tr>
                    <td>{{ ++$i }}</td>
                    <td colspan="3">{{ $description }}</td>
                    <td>{{ $sku }}</td>
                    <td>{{ number_format($unit_cost, 3) }}</td>
                    <td>{{ $qty }}</td>
                    <td style="text-align: right;">{{ number_format($taxable_value,3) }}</td>
                    <td>{{ $tax_name }}</td>
                    <td style="text-align: right;">{{ number_format($tax_amt,3) }}</td>
                    <td style="text-align: right;">{{ number_format($total_amount,3) }}</td>
                </tr>
                @php
                    $tot_qty += $qty;
                    $tot_sales_price += $unit_cost;
                    $tot_tax_amt += $tax_amt;
                    $tot_unit_total_cost += $unit_cost;
                    $tot_total_cost += $total_amount;
                    $taxable_value_total += $taxable_value;
                @endphp
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="6"></td>
                <td>{{ $tot_qty }}</td>
                <td style="text-align: right;"><b>{{ number_format($taxable_value_total,3) }}</b></td>
                <td></td>
                <td style="text-align: right;"><b>{{ number_format($tot_tax_amt,3) }}</b></td>
                <td style="text-align: right;"><b>{{ number_format($tot_total_cost,3) }}</b></td>
            </tr>
            <tr>
                <td colspan="11">
                    <span class="amt-in-word">Amount in words: <i style="font-weight:bold; text-transform: uppercase;">OMANI RIAL {{-- Add number to words logic here --}}</i></span>
                </td>
            </tr>
        </tfoot>
    </table>
    <h4>Please Note:</h4>
    <p style="font-size:12px;">{{ $deliveryNote->customer->customer_notes ?? '' }}</p>
</div>
</body>
</html>
