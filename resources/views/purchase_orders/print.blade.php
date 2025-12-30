<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <style>
        table, th, td { border: 1px solid black; border-collapse: collapse; }
        th, td { padding: 5px; text-align: left; vertical-align:top; }
        body { font-size:10px; }
        .next-page { page-break-before: always; }
        header { position:fixed; top:0px; left: 0px; width:100%; height: 80px; margin-bottom: 10px; }
        footer { position:fixed; bottom: 0px; left: 0px; width:100%; height: 100px; }
        * { font-family: DejaVu Sans, sans-serif; }
    </style>
</head>
<body style="padding-top:70px; padding-bottom: 100px;">
<header>
    <img src="{{ public_path('theme/images/headerlogo.jpg') }}" height="65">
</header>
<footer>
    <img src="https://app.blueraynational.com/theme/images/footer.jpg" width="100%">
</footer>
<div class="maincon">
    <div class="heading-container">
        <div class="heading-box">
            <h3 style="text-transform:uppercase; text-align:center;">Purchase Invoice</h3>
            <hr>
            <h3 style="text-align:center;">VAT IN OM1100048734</h3>
        </div>
    </div>
    <div class="doc-details">
        <table>
            <tr>
                <td><b>Purchase Order No: {{ $purchaseOrder->po_no }}</b></td>
                <td><b>Purchase Date: {{ $purchaseOrder->po_date }}</b></td>
            </tr>
            <tr>
                <td>
                    <i>Supplier Details</i><br/>
                    <b>{{ $purchaseOrder->supplier->supplier_name }}</b><br/>
                    {{ $purchaseOrder->supplier->address }}<br/>
                    @if($purchaseOrder->supplier->mobile) Mobile: {{ $purchaseOrder->supplier->mobile }}<br/>@endif
                    @if($purchaseOrder->supplier->email) Email: {{ $purchaseOrder->supplier->email }}<br/>@endif
                    @if($purchaseOrder->supplier->gstin) GST: {{ $purchaseOrder->supplier->gstin }}<br/>@endif
                    @if($purchaseOrder->supplier->tax_number) Tax No: {{ $purchaseOrder->supplier->tax_number }}<br/>@endif
                </td>
                <td>
                    <i>Shipping Address</i><br/>
                    <b>{{ $company->company_name }}</b><br/>
                    {{ $company->address }}<br/>
                    Mobile: {{ $company->mobile }}<br/>
                    @if($company->email) Email: {{ $company->email }}<br/>@endif
                </td>
            </tr>
        </table>
        <h4>Please Supply Following Items</h4>
        <table width="100%">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Item Name</th>
                    <th>Item Code</th>
                    <th style="text-align: right;">Purchase Price</th>
                    <th style="text-align: center;">Quantity</th>
                    <th style="text-align: right;">Total Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($purchaseOrder->items as $i => $orderItem)
                <tr>
                    <td>{{ $i+1 }}</td>
                    <td>{{ $orderItem->item->item_name ?? '-' }}</td>
                    <td>{{ $orderItem->item->item_code ?? '-' }}</td>
                    <td style="text-align: right;">{{ number_format($orderItem->rate, 3) }}</td>
                    <td style="text-align: center;">{{ $orderItem->qty }}</td>
                    <td style="text-align: right;">{{ number_format($orderItem->amount, 3) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" style="text-align: right;font-weight: bold;">Total</td>
                    <td style="text-align: center;">{{ $purchaseOrder->items->sum('qty') }}</td>
                    <td style="text-align: right;">{{ number_format($purchaseOrder->items->sum('amount'), 3) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
</body>
</html>
