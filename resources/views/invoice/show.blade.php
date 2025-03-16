@extends('layout.app')

@section('content')
<div class="container">
    <h2>Invoice {{ $invoice->id }}</h2>
    <a href="{{ route('invoice.pdf', $invoice->id) }}"  class="btn btn-sm btn-info">Print</a>
    <p><strong>Customer:</strong> {{ $invoice->name }}</p>
    <p><strong>Total Amount:</strong> ${{ number_format($invoice->total_amount, 2) }}</p>

    <h4>Invoice Items</h4>
    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>Product</th>
                <th>Quantity</th>
                <th>Price</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($invoice->invoiceItems as $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item->product_name }}</td>
                <td>{{ $item->quantity }}</td>
                <td>${{ number_format($item->price, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
