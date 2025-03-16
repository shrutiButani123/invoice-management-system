@extends('layout.app')

@section('content')
<nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Invoice Management</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link active" href="{{route('user.dashboard')}}">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{route('invoices')}}">Inovices</a></li>
                </ul>
            </div>

            <ul class="list-unstyled mb-0">
                <li class="list-inline-item mb-0 ms-1">
                    <div class="dropdown dropdown-primary">
                        <button type="button" class="btn btn-soft-light dropdown-toggle p-0" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{ auth()->user()->name}}</button>
                        <div class="dropdown-menu dd-menu dropdown-menu-end bg-white shadow border-0 mt-3 py-3" style="min-width: 200px;">
                            <a class="dropdown-item d-flex align-items-center text-dark pb-3" href="profile.html">                                    
                                <div class="flex-1 ms-2">
                                    <span class="d-block">{{ auth()->user()->name}}</span>
                                </div>
                            </a>
                            
                            <form id="logout-form"  action="{{ route('logout') }}" method="POST" >
                                @csrf
                                <button type="submit" class="dropdown-item text-dark dropdown-buttonn"><span class="mb-0 d-inline-block me-1"><i class="ti ti-logout"></i></span>Logout</button>
                            </form>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </nav>

<div class="container my-5">
<div class="row justify-content-center align-items-center">
<div class="card rounded shadow p-4">
    <div class="d-flex justify-content-between align-items-center">
    <h2>Invoice {{ $invoice->id }}</h2>
    <a href="{{ route('invoice.pdf', $invoice->id) }}"  class="btn btn-sm btn-info">Print</a>
    </div>
    <p><strong>Customer:</strong> {{ $invoice->customer_name }}</p>
    <p><strong>Total Amount:</strong> {{ number_format($invoice->total_amount, 2) }}</p>
    <p><strong>Payment Status:</strong> {{ ($invoice->status) }}</p>

    <h4>Invoice Items</h4>
    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>Items</th>
                <th>Quantity</th>
                <th>Price</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($invoice->invoiceItems as $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item->item->name }}</td>
                <td>{{ $item->quantity }}</td>
                <td>${{ number_format($item->price, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    </div>
    </div>
</div>
@endsection
