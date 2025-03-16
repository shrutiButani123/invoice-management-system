<!DOCTYPE html>
<html>
<head>
    <title>Invoice #{{ $invoice->id }}</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .invoice-box { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .invoice-box td, .invoice-box th { border: 1px solid #ddd; padding: 8px; }
        .invoice-box th { background-color: #f2f2f2; text-align: left; }
        .total { font-weight: bold; font-size: 16px; }
    </style>
</head>
<body>
    <h2>Invoice #{{ $invoice->id }}</h2>
    <p><strong>Customer:</strong> {{ $invoice->customer_name }}</p>
    <p><strong>Invoice Date:</strong> {{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d-m-Y') }}</p>
    <p><strong>Invoice Due Date:</strong> {{ \Carbon\Carbon::parse($invoice->due_date)->format('d-m-Y') }}</p>

    <table class="invoice-box">
        <tr>
            <th>Item</th>
            <th>Quantity</th>
            <th>Price</th>
            <th>Tax</th>
            <th>Total</th>
        </tr>
        @foreach($invoice->invoiceItems as $item)
            <tr>
                <td>{{ $item->item->name }}</td>
                <td>{{ $item->quantity }}</td>
                <td>${{ number_format($item->price, 2) }}</td>
                <td>${{ number_format($item->tax, 2) }}</td>
                <td>${{ number_format($item->subtotal, 2) }}</td>
            </tr>
        @endforeach
    </table>

    <p class="total">Total Amount: ${{ number_format($invoice->grand_total, 2) }}</p>
</body>
</html>
