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
<table align="center" width="100%" height='100%'>
    <thead>
      <tr>
          <th colspan="5" rowspan="2" style="padding-left: 15px;">
            <b>{{ $company_name ?? '' }}</b><br/>
            {{ __('address') }} : {{ $company_address ?? '' }}<br/>
            {{ $company_country ?? '' }}<br/>
            {{ __('mobile') }}:{{ $company_mobile ?? '' }}<br/>
            @if(!empty($company_email)){{ __('email') }}: {{ $company_email }}<br>@endif
            @if(!empty($company_gst_no)){{ __('gst_number') }}: {{ $company_gst_no }}<br>@endif
            @if(!empty($company_vat_no)){{ __('vat_number') }}: {{ $company_vat_no }}<br>@endif
          </th>
          <th colspan="5" rowspan="1"><b style="text-transform: capitalize;">{{ __('sales_invoice') }}</b> ({{ $return_status ?? '' }})</th>
      </tr>
      <tr>
          <th colspan="3" rowspan="1">
              {{ __('invoice_no') }} : {{ $return_code ?? '' }}<br>
              {{ __('reference_no') }} : {{ $reference_no ?? '' }}
          </th>  
          <th colspan="2" rowspan="1">{{ __('date') }} : {{ $return_date ? \Carbon\Carbon::parse($return_date)->format('d-m-Y') : '' }} {{ $created_time ?? '' }}</th>
      </tr>
      <tr>
        <td colspan="5" style="padding-left: 15px;">
            <b>{{ __('customer_address') }}</b><br/>
            {{ __('name') }}: {{ $customer_name ?? '' }}<br/>
            {{ __('mobile') }}: {{ $customer_mobile ?? '' }}
            @if(!empty($customer_address)){{ $customer_address }}@endif
            @if(!empty($customer_country)),{{ $customer_country }}@endif
            @if(!empty($customer_state)),{{ $customer_state }}@endif
            @if(!empty($customer_city)),{{ $customer_city }}@endif
            @if(!empty($customer_postcode)) -{{ $customer_postcode }}@endif
            <br>
            @if(!empty($customer_email)){{ __('email') }}: {{ $customer_email }}<br>@endif
            @if(!empty($customer_gst_no)){{ __('gst_number') }}: {{ $customer_gst_no }}<br>@endif
            @if(!empty($customer_tax_number)){{ __('tax_number') }}: {{ $customer_tax_number }}<br>@endif
        </td>
        <td colspan="5" style="padding-left: 15px;">
            <b>{{ __('shipping_address') }}</b><br/>
            {{ __('name') }}: {{ $customer_name ?? '' }}<br/>
            {{ __('mobile') }}: {{ $customer_mobile ?? '' }}
            @if(!empty($customer_address)){{ $customer_address }}@endif
            @if(!empty($customer_country)),{{ $customer_country }}@endif
            @if(!empty($customer_state)),{{ $customer_state }}@endif
            @if(!empty($customer_city)),{{ $customer_city }}@endif
            @if(!empty($customer_postcode)) -{{ $customer_postcode }}@endif
            <br>
            @if(!empty($customer_email)){{ __('email') }}: {{ $customer_email }}<br>@endif
            @if(!empty($customer_gst_no)){{ __('gst_number') }}: {{ $customer_gst_no }}<br>@endif
            @if(!empty($customer_tax_number)){{ __('tax_number') }}: {{ $customer_tax_number }}<br>@endif
        </td>
      </tr>
      <tr>
        <th>#</th>
        <th>{{ __('item_name') }}</th>
        <th>{{ __('sales_price') }}</th>
        <th>{{ __('quantity') }}</th>
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
            $tot_unit_total_cost=0;
            $tot_total_cost=0;
        @endphp
        @foreach($items as $item)
            @php
                $discount = empty($item->discount_input) || $item->discount_input==0 ? '-' : $item->discount_input.'%';
                $discount_amt = empty($item->discount_amt) || $item->discount_input==0 ? '-' : $item->discount_amt;
            @endphp
            <tr>
                <td>{{ ++$i }}</td>
                <td>{{ $item->item_name }}</td>
                <td>{{ $item->price_per_unit }}</td>
                <td>{{ $item->return_qty }}</td>
                <td>{{ $item->tax }}%<br>{{ $item->tax_name }}</td>
                <td style="text-align: right;">{{ $item->tax_amt }}</td>
                <td style="text-align: right;">{{ $discount }}</td>
                <td style="text-align: right;">{{ $discount_amt }}</td>
                <td style="text-align: right;">{{ $item->unit_total_cost }}</td>
                <td style="text-align: right;">{{ $item->total_cost }}</td>
            </tr>
            @php
                $tot_qty += $item->return_qty;
                $tot_sales_price += $item->price_per_unit;
                $tot_tax_amt += $item->tax_amt;
                $tot_discount_amt += $item->discount_amt;
                $tot_unit_total_cost += $item->unit_total_cost;
                $tot_total_cost += $item->total_cost;
            @endphp
        @endforeach
    </tbody>
    <tfoot>
      <tr>
        <td colspan="3" style="text-align: center;font-weight: bold;">{{ __('total') }}</td>
        <td>{{ $tot_qty }}</td>
        <td>-</td>
        <td style="text-align: right;"><b>{{ number_format($tot_tax_amt,2,'.','') }}</b></td>
        <td>-</td>
        <td style="text-align: right;"><b>{{ number_format($tot_discount_amt,2,'.','') }}</b></td>
        <td style="text-align: right;"><b>{{ number_format($tot_unit_total_cost,2,'.','') }}</b></td>
        <td style="text-align: right;"><b>{{ number_format($tot_total_cost,2,'.','') }}</b></td>
      </tr>
      <tr>
        <td colspan="9" style="text-align: right;"><b>{{ __('subtotal') }}</b></td>
        <td style="text-align: right;"><b>{{ number_format(round($subtotal),2,'.','') }}</b></td>
      </tr>
      <tr>
        <td colspan="9" style="text-align: right;"><b>{{ __('other_charges') }}</b></td>
        <td style="text-align: right;"><b>{{ number_format(round($other_charges_amt),2,'.','') }}</b></td>
      </tr>
      <tr>
        <td colspan="9" style="text-align: right;"><b>{{ __('discount_on_all') }} ({{ $discount_to_all_input.' '.$discount_to_all_type }})</b></td>
        <td style="text-align: right;"><b>{{ number_format(round($tot_discount_to_all_amt),2,'.','') }}</b></td>
      </tr>
      <tr>
        <td colspan="9" style="text-align: right;"><b>{{ __('grand_total') }}</b></td>
        <td style="text-align: right;"><b>{{ number_format(round($grand_total),2,'.','') }}</b></td>
      </tr>
      <tr>
        <td colspan="10">
            <span class='amt-in-word'>Amount in words: <i style='font-weight:bold;'>{{-- Add number to words logic here --}} Only</i></span>
        </td>
      </tr>
      <tr>
        <td colspan="5" style="height:100px;">
          <b>{{ __('customer_signature') }}</b><br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>
        </td>
        <td colspan="5">
          <b>{{ __('authorised_signature') }}</b><br/><br/><br/><br/><br/>
        </td>
      </tr>
      @if(!empty($sales_invoice_footer_text))
      <tr style="border-top: 1px solid;">
        <td colspan="10" style="text-align: center;">
          <b>{{ $sales_invoice_footer_text }}</b>
        </td>
      </tr>
      @endif
    </tfoot>
</table>
</body>
</html>