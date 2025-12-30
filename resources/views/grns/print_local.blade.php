<!DOCTYPE html>
<html>
<head>
    <style>
        table, th, td {
            border: 1px solid black;
            border-collapse: collapse;
        }
        th, td {
            padding: 5px;
            text-align: left;
            vertical-align: top;
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
<body style="padding-top:70px; padding-bottom: 100px;">
<header>
    <img src="{{ asset('theme/images/headerlogo.jpg') }}" height="65">
</header>
<footer>
    <img src="https://app.blueraynational.com/theme/images/footer.jpg" width="100%">
</footer>
<div class="maincon">
    <div class="heading-container">
        <div class="heading-box">
            <h3 style="text-transform:uppercase; text-align:center;">GRN Invoice - Local</h3>
            <hr>
            <h3 style="text-align:center;">VAT IN OM1100048734</h3>
        </div>
    </div>
    <div class="doc-details">
        <table>
            <tr>
                <td><b>GRN No: {{ $grn->grn_no }}</b></td>
                <td><b>GRN Date: {{ $grn->grn_date->format('d-m-Y') }}</b></td>
            </tr>
            <tr>
                <td>
                    <i>Supplier Details</i><br/>
                    <b>{{ $grn->supplier->supplier_name ?? '' }}</b><br/>
                    {{ $grn->supplier->address ?? '' }}<br/>
                    @if(!empty($grn->supplier->mobile))Mobile: {{ $grn->supplier->mobile }}<br/>@endif
                    @if(!empty($grn->supplier->email))Email: {{ $grn->supplier->email }}<br/>@endif
                    @if(!empty($grn->supplier->gstin))GST No: {{ $grn->supplier->gstin }}<br/>@endif
                    @if(!empty($grn->supplier->tax_number))Tax No: {{ $grn->supplier->tax_number }}<br/>@endif
                </td>
                <td>
                    <i>Shipping Address</i><br/>
                    <b>{{ $company_name ?? '' }}</b><br/>
                    {{ $company_address ?? '' }}<br/>
                    Mobile: {{ $company_mobile ?? '' }}<br/>
                    @if(!empty($company_email))Email: {{ $company_email }}<br/>@endif
                </td>
            </tr>
        </table>
        <h4>Please Supply Following Items</h4>
        <table width="100%">
            <thead>
                <tr>
                    <th>#</th>
                    <th colspan="2">Item Name</th>
                    <th style="text-align: right;">Purchase Price</th>
                    <th style="text-align: center;">Quantity</th>
                    <th style="text-align: right;">Taxable Value</th>
                    <th style="text-align: right;">Discount</th>
                    <th style="text-align: right;">Discount Amount</th>
                    <th style="text-align: right;">Total Amount</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $i = 0;
                    $tot_qty = 0;
                    $tot_purchase_price = 0;
                    $tot_tax_amt = 0;
                    $tot_discount_amt = 0;
                    $tot_total_cost = 0;
                    $tot_taxable_value = 0;
                @endphp
                @foreach($grn->items as $item)
                    @php
                        $purchase_price = $item->base_cost;
                        $tax_amount = 0;
                        $total_amount = $item->landed_cost_per_unit * $item->qty_received;
                        $taxable_value = $purchase_price * $item->qty_received;
                        $discount = 0;
                    @endphp
                    <tr>
                        <td>{{ ++$i }}</td>
                        <td colspan="2">{{ $item->item->item_name ?? '' }} {{ $item->item->item_code ?? '' }}</td>
                        <td style="text-align: right;">{{ number_format($purchase_price, 3, '.', '') }}</td>
                        <td style="text-align: center;">{{ round($item->qty_received) }}</td>
                        <td style="text-align: right;">{{ number_format($taxable_value, 3) }}</td>
                        <td style="text-align: right;">{{ $discount }}%</td>
                        <td style="text-align: right;">0.000</td>
                        <td style="text-align: right;">{{ number_format($total_amount, 3, '.', '') }}</td>
                    </tr>
                    @php
                        $tot_qty += $item->qty_received;
                        $tot_purchase_price += $purchase_price * $item->qty_received;
                        $tot_tax_amt += $tax_amount;
                        $tot_discount_amt += 0;
                        $tot_total_cost += $total_amount;
                        $tot_taxable_value += $taxable_value;
                    @endphp
                @endforeach
            </tbody>
            <tfoot>
                <tr style="border-bottom:1px solid;">
                    <td colspan="3" style="text-align: right;font-weight: bold;">Total</td>
                    <td style="text-align: right;"><b>{{ number_format($tot_purchase_price, 3, '.', '') }}</b></td>
                    <td style="text-align: center;">{{ $tot_qty }}</td>
                    <td style="text-align: right;"><b>{{ number_format($tot_taxable_value, 3, '.', '') }}</b></td>
                    <td></td>
                    <td style="text-align: right;"><b>{{ number_format($tot_tax_amt, 3, '.', '') }}</b></td>
                    <td style="text-align: right;"><b>{{ number_format($tot_total_cost, 3, '.', '') }}</b></td>
                </tr>
                <tr>
                    <td colspan="8" style="text-align: right; padding-top:10px;"><b>Total Excl Vat:</b></td>
                    <td style="text-align: right; padding-top:10px;"><b>{{ number_format($tot_purchase_price, 3, '.', '') }} {{ $grn->currency ?? '' }}</b></td>
                </tr>
                <tr>
                    <td colspan="8" style="text-align: right;"><b>VAT @ 5% AMOUNT</b></td>
                    <td style="text-align: right;"><b>{{ number_format($tot_tax_amt, 3, '.', '') }} {{ $grn->currency ?? '' }}</b></td>
                </tr>
                <tr style="border-top: 1px solid;  border-bottom: double;">
                    <td colspan="7"></td>
                    <td style="text-align: right;"><b>TOTAL INCL VAT</b></td>
                    <td style="text-align: right;"><b>{{ number_format($tot_total_cost, 3, '.', '') }} {{ $grn->currency ?? '' }}</b></td>
                </tr>
                <tr>
                    <td colspan="8">
                        <span class="amt-in-word">Amount in words: <i style="font-weight:bold; text-transform: uppercase;">OMANI RIAL {{-- Add number to words logic here --}}</i></span>
                    </td>
                </tr>
                <tr>
                    <td colspan="9">
                        @if(!empty($grn->supplier->supplier_notes))
                            Payment Note: {{ $grn->supplier->supplier_notes }}
                        @endif
                    </td>
                </tr>
            </tfoot>
        </table>
        <div class="double-col-5" style="float:right;">
            <b>Authorised Signature</b><br/><br/><br/><br/><br/>
        </div>
    </div>
</div>
</body>
</html>
