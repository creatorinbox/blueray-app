
<!DOCTYPE html>
<html>
<head>
    <style>
        table, th, td {
            border: 1px solid black;
            border-collapse: collapse;
        }
        th, td {
            padding: 3px;
            text-align: left;
            vertical-align:top;
        }
        body{
          word-wrap: break-word;
          font-size:10px;
        }
        .doc-details{
            display:grid;
            padding: 0 10px;
            border:1px solid #000;
            border-bottom:none;
            grid-gap:10px;
        }
        .double-col-1{
            grid-column: 1 / span 2;
            padding:10px 0;
            border-right:1px solid #000;
        }
        .double-col-2{
            grid-column: 3;
            padding:10px 0;
        }
        .sign-box{
            display:grid;
            padding: 0 10px;
            grid-template-columns: auto auto;
            grid-gap:10px;
            border:1px solid #000;
            border-top:none;
            margin-bottom:10px;
        }
        .double-col-4{
            padding:10px 0;
            border-right:1px solid #000;
        }
        .double-col-5{
            padding:10px 0;
        }
        @page { 
            width:210mm;
            height:297mm;
            margin: 45mm 15mm 40mm 15mm; }
        * { font-family: DejaVu Sans, sans-serif; }
    </style>
</head>
<body onload="window.print();">
<div class="maincon">
    <div class="doc-title">
        <h3 style="text-align:center; text-transform:uppercase;">
            {{ strtolower($invoice->invoice_status ?? '') == 'final' ? 'Tax Invoice' : 'Proforma Invoice' }}
            @if(strtolower($invoice->invoice_type ?? '') == 'cash') - Cash @endif
        </h3>
        <h4 style="text-align:center;">VAT IN OM1100048734</h4>
    </div>
    <div class="doc-details">
        <div class="double-col-1">
            Name: <b>{{ $invoice->customer_name ?? '' }}</b><br/>
            @if(!empty($invoice->customer_mobile))Mobile: {{ $invoice->customer_mobile }}<br>@endif
            @if(!empty($invoice->address))Address: {{ $invoice->address }}<br>@endif
            @if(!empty($invoice->postcode))Postal Code:{{ $invoice->postcode }}<br>@endif
            @if(!empty($invoice->city)), {{ $invoice->city }}<br>@endif
            @if(!empty($invoice->state)), {{ $invoice->state }}<br>@endif
            @if(!empty($invoice->country)), {{ $invoice->country }}<br>@endif
            @if(!empty($invoice->email))Email: {{ $invoice->email }}<br>@endif
            @if(!empty($invoice->gstin))GST No: {{ $invoice->gstin }}<br>@endif
            @if(!empty($invoice->tax_number))Tax No: {{ $invoice->tax_number }}<br>@endif
        </div>
        <div class="double-col-2">
            Invoice No: BRN-{{ date('Y') }}-{{ $invoice->invoice_no ?? '' }}-{{ $invoice->paid_version ?? '' }}<br>
            Date: {{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d-m-Y') }} {{ $invoice->created_time ?? '' }}<br>
            @if(!empty($invoice->po_number))PO Number: {{ $invoice->po_number }}<br>@endif
            @if(!empty($invoice->quotation))
                @if(!empty($invoice->quotation->sales_code))Quotation Number: {{ $invoice->quotation->sales_code }}<br>@endif
                @if(!empty($invoice->quotation->sales_date))Quotation Date: {{ \Carbon\Carbon::parse($invoice->quotation->sales_date)->format('d-m-Y') }}<br>@endif
                @if(!empty($invoice->quotation->po_date))PO Date: {{ $invoice->quotation->po_date }}<br>@endif
                @if(!empty($invoice->quotation->po_number))PO Number: {{ $invoice->quotation->po_number }}<br>@endif
                Reference No: {{ $invoice->reference_no ?? '' }}<br>
            @endif
        </div>
    </div>
    <table align="center" width="100%" height='100%'>
        <thead>
            <tr>
                <th>SL NO</th>
                <th colspan="4">Item Name</th>
                <th>Unit Cost</th>
                <th>Quantity</th>
                @if(strtolower($invoice->invoice_type ?? '') == 'full')
                    <th>Taxable Value</th>
                @else
                    <th>Total</th>
                @endif
                <th>Discount</th>
                <th>Discount Amount</th>
                @if(strtolower($invoice->invoice_type ?? '') == 'full')
                    <th>Total Amount</th>
                @endif
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
            @foreach($items as $item)
                @php
                    $discount = (property_exists($item, 'discount_input') && $item->discount_input) ? $item->discount_input.'%' : '0';
                    $discount_amt = property_exists($item, 'discount_amt') ? ($item->discount_amt ?? 0) : 0;
                    $serial_number = property_exists($item, 'serial_number') && $item->serial_number ? '<br>-'.nl2br($item->serial_number) : '';
                    $item_name = $item->itemname ?? $item->item_name ?? '';
                    $item_code = $item->item_code ?? '';
                    $unit_cost = $item->unit_price ?? 0;
                    $qty = $item->quantity ?? 0;
                    $taxable_value = ($item->total_price ?? 0) - ($item->tax_amt ?? 0);
                    $total_amount = $item->total_price ?? 0;
                @endphp
                <tr>
                    <td>{{ ++$i }}</td>
                    <td colspan="4" style="width:350px;">{{ $item_name }} - {{ $item_code }}{!! $serial_number !!}</td>
                    <td>{{ number_format($unit_cost, 3) }}</td>
                    <td>{{ $qty }}</td>
                    <td style="text-align: right;">{{ number_format($taxable_value,3) }}</td>
                    <td style="text-align: right;">{{ $discount }}</td>
                    <td style="text-align: right;">{{ number_format($discount_amt,3) }}</td>
                    @if(strtolower($invoice->invoice_type ?? '') == 'full')
                        <td style="text-align: right;">{{ number_format($total_amount,3) }}</td>
                    @endif
                </tr>
                @php
                    $tot_qty += $qty;
                    $tot_sales_price += $unit_cost;
                    $tot_tax_amt += $item->tax_amt ?? 0;
                    $tot_discount_amt += $discount_amt;
                    $tot_unit_total_cost += $item->unit_total_cost ?? 0;
                    $tot_total_cost += $total_amount;
                    $taxable_value_total += $taxable_value;
                @endphp
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td></td>
                <td colspan="4"></td>
                <td>-</td>
                <td>{{ $tot_qty }}</td>
                <td style="text-align: right;"><b>{{ number_format($taxable_value_total,3) }}</b></td>
                <td></td>
                <td style="text-align: right;"><b>{{ number_format($tot_discount_amt,3) }}</b></td>
                @if(strtolower($invoice->invoice_type ?? '') == 'full')
                    <td style="text-align: right;"><b>{{ number_format($tot_total_cost,3) }}</b></td>
                @endif
            </tr>
            <tr>
                <td colspan="11">
                    <span class="amt-in-word">Amount in words: <i style="font-weight:bold; text-transform: uppercase;">OMANI RIAL {{-- Add number to words logic here --}}</i></span>
                </td>
            </tr>
        </tfoot>
    </table>
    <div class="sign-box">
        <div class="double-col-4">
            <b>For {{ $company_name ?? '' }}</b>
            <p>Delivery Made To/ Work Done At</p><br><br><br><br><br>
            <p style="margin-right:20px;"><span style="margin-right:120px;">Sales Division </span>Stores Dept.</p>
        </div>
        <div class="double-col-5">
            <b>For {{ $invoice->customer_name ?? '' }}</b>
            <p>We have received the Tax Invoice above stated materials in good order/condition/the above stated repairs/services have been carried out to our satisfaction on  {{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d-m-Y') }}</p>
            <p>Signature :</p><br>
            <p>Name :</p>
        </div>
    </div>
    @if(!empty($sales_invoice_footer_text))
        <tr style="border-top: 1px solid;">
            <td colspan="12" style="text-align: center;">
                <b>{{ $sales_invoice_footer_text }}</b>
            </td>
        </tr>
    @endif
    @if(isset($payments) && count($payments))
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <table class="table table-hover table-bordered" style="width:100%">
                        <h4 class="box-title text-info">Payments Information:</h4>
                        <thead>
                            <tr class="bg-purple">
                                <th>#</th>
                                <th>Date</th>
                                <th>Payment Type</th>
                                <th>Payment Note</th>
                                <th>Payment</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $i=1; $total_paid=0; @endphp
                            @foreach($payments as $payment)
                                <tr class="text-center text-bold">
                                    <td>{{ $i++ }}</td>
                                    <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('d-m-Y') }}</td>
                                    <td>{{ $payment->payment_type }}</td>
                                    <td>{{ $payment->payment_note }}</td>
                                    <td class="text-right">{{ number_format($payment->payment,3) }}</td>
                                </tr>
                                @php $total_paid += $payment->payment; @endphp
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>
</body>
</html>
