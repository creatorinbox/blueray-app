<!DOCTYPE html>
<html>
<head>
    <style>
        table, th, td {
            border: 1px solid black;
            border-collapse: collapse;
            font-family: 'Open Sans', 'Martel Sans', sans-serif;
        }
        th, td {
            padding: 5px;
            text-align: left;
            vertical-align:top;
        }
        body{
          word-wrap: break-word;
        }
    </style>
</head>
<body onload="window.print();">
<div class="printableArea">
    <h2 style="text-align:center;">
        <i class="fa fa-globe"></i> Job Card
        <small style="float:right;">Date: {{ $sales_date ? \Carbon\Carbon::parse($sales_date)->format('d-m-Y') : '' }} {{ $created_time ?? '' }}</small>
    </h2>
    <table width="100%" style="margin-bottom:20px;">
        <tr>
            <td width="33%">
                <b>{{ $company_name ?? '' }}</b><br>
                {{ $company_address ?? '' }}, {{ __('city') }}:{{ $company_city ?? '' }}<br>
                {{ __('phone') }}: {{ $company_phone ?? '' }}, {{ __('mobile') }}: {{ $company_mobile ?? '' }}<br>
                @if(!empty($company_email)){{ __('email') }}: {{ $company_email }}<br>@endif
                @if(!empty($company_gst_no)){{ __('gst_number') }}: {{ $company_gst_no }}<br>@endif
                @if(!empty($company_vat_no)){{ __('vat_number') }}: {{ $company_vat_no }}<br>@endif
                @if(!empty($company_pan_no)){{ __('vat_number') }}: {{ $company_pan_no }}<br>@endif
            </td>
            <td width="33%">
                <b>{{ __('customer_details') }}</b><br>
                <b>{{ $customer_name ?? '' }}</b><br>
                @if(!empty($customer_address)){{ $customer_address }}@endif
                @if(!empty($customer_country)),{{ $customer_country }}@endif
                @if(!empty($customer_state)),{{ $customer_state }}@endif
                @if(!empty($customer_city)),{{ $customer_city }}@endif
                @if(!empty($customer_postcode)) -{{ $customer_postcode }}@endif
                <br>
                @if(!empty($customer_mobile)){{ __('mobile') }}: {{ $customer_mobile }}<br>@endif
                @if(!empty($customer_phone)){{ __('phone') }}: {{ $customer_phone }}<br>@endif
                @if(!empty($customer_email)){{ __('email') }}: {{ $customer_email }}<br>@endif
                @if(!empty($customer_gst_no)){{ __('gst_number') }}: {{ $customer_gst_no }}<br>@endif
                @if(!empty($customer_tax_number)){{ __('tax_number') }}: {{ $customer_tax_number }}<br>@endif
            </td>
            <td width="33%">
                <b>{{ __('invoice') }} #{{ $sales_code ?? '' }}</b><br>
                <b>{{ __('sales_status') }} :{{ $sales_status ?? '' }}</b><br>
                <b>Invoice Number :{{ $invoice_no ?? '' }}</b><br>
            </td>
        </tr>
    </table>
    <table class="table table-striped records_table table-bordered" width="100%">
        <thead>
            <tr>
                <th>#</th>
                <th>{{ __('item_name') }}</th>
                <th>{{ __('unit_price') }}</th>
                <th>{{ __('quantity') }}</th>
                <th>{{ __('net_cost') }}</th>
                <th>{{ __('tax') }}</th>
                <th>{{ __('tax_amount') }}</th>
                <th>{{ __('discount') }}</th>
                <th>{{ __('discount_amount') }}</th>
                <th>{{ __('unit_cost') }}</th>
                <th>{{ __('total_amount') }}</th>
            </tr>
        </thead>
        <tbody>
            @php
                $i=0;
                $tot_qty=0;
                $tot_sales_price=0;
                $tot_tax_amt=0;
                $tot_discount_amt=0;
                $tot_total_cost=0;
            @endphp
            @foreach($items as $item)
                @php
                    $str = ($item->tax_type=='Inclusive')? 'Inc.' : 'Exc.';
                    $discount = (empty($item->discount_input)||$item->discount_input==0)? '0':$item->discount_input.'%';
                    $discount_amt = (empty($item->discount_amt)||$item->discount_input==0)? '0':$item->discount_amt;
                @endphp
                <tr>
                    <td>{{ ++$i }}</td>
                    <td>
                        {{ $item->item_name }}
                        @if(!empty($item->serial_number))<br><i>[{!! nl2br($item->serial_number) !!}]</i>@endif
                        @if(!empty($item->description))<br><i>[{!! nl2br($item->description) !!}]</i>@endif
                    </td>
                    <td class="text-right">{{ number_format($item->price_per_unit,2,'.','') }}</td>
                    <td>{{ $item->sales_qty }}</td>
                    <td class="text-right">{{ number_format($item->price_per_unit * $item->sales_qty,2,'.','') }}</td>
                    <td>{{ $item->tax }}%<br>{{ $item->tax_name }}[{{ $str }}]</td>
                    <td class="text-right">{{ number_format($item->tax_amt,2,'.','') }}</td>
                    <td class="text-right">{{ $discount }}</td>
                    <td class="text-right">{{ number_format($discount_amt,2,'.','') }}</td>
                    <td class="text-right">{{ number_format($item->unit_total_cost,2,'.','') }}</td>
                    <td class="text-right">{{ number_format($item->total_cost,2,'.','') }}</td>
                </tr>
                @php
                    $tot_qty += $item->sales_qty;
                    $tot_sales_price += $item->price_per_unit;
                    $tot_tax_amt += $item->tax_amt;
                    $tot_discount_amt += $item->discount_amt;
                    $tot_total_cost += $item->total_cost;
                @endphp
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2" class="text-center">Total</td>
                <td>{{ number_format($tot_sales_price,3,'.','') }}</td>
                <td class="text-left">{{ $tot_qty }}</td>
                <td>-</td>
                <td>-</td>
                <td>{{ number_format($tot_tax_amt,3,'.','') }}</td>
                <td>-</td>
                <td>{{ number_format($tot_discount_amt,3,'.','') }}</td>
                <td>-</td>
                <td>{{ number_format($tot_total_cost,3,'.','') }}</td>
            </tr>
        </tfoot>
    </table>
    <div style="margin-top:20px;">
        <b>{{ __('note') }}:</b> {{ $sales_note ?? '' }}
    </div>
    <table width="50%" align="right" style="margin-top:20px;">
        <tr>
            <th class="text-right">{{ __('subtotal') }}</th>
            <th class="text-right" style="padding-left:10%;">
                <h4><b id="subtotal_amt" name="subtotal_amt">{{ $subtotal ?? '' }}</b></h4>
            </th>
        </tr>
        <tr>
            <th class="text-right">{{ __('other_charges') }}</th>
            <th class="text-right" style="padding-left:10%;">
                <h4><b id="other_charges_amt" name="other_charges_amt">{{ $other_charges_amt ?? '' }}</b></h4>
            </th>
        </tr>
        <tr>
            <th class="text-right">{{ __('grand_total') }}</th>
            <th class="text-right" style="padding-left:10%;">
                <h4><b id="total_amt" name="total_amt">{{ $grand_total ?? '' }}</b></h4>
            </th>
        </tr>
    </table>
</div>
</body>
</html>