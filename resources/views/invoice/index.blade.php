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
<div class="container-fluid">
    <div class="layout-specing">
        <div class="flash-message"></div>

        <div class="row justify-content-center">
            <div class="col-lg-10 mt-4">
                <div class="card rounded shadow">
                    <div class="p-4 border-bottom">
                        <div class="d-flex justify-content-between">
                            <h4 class="card-title">Invoice List</h4>
                            <a href="{{ route('invoice.create') }}" class="btn btn-primary">Create New Invoice</a>
                        </div>
                    </div>

                    <div class="table-responsive p-4">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Customer Name</th>
                                    <th>Invoice Date</th>
                                    <th>Due Date</th>
                                    <th>Status</th>
                                    <th>Total Amount</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($invoices as $invoice)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $invoice->customer_name }}</td>
                                        <td>{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d M Y') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($invoice->due_date)->format('d M Y') }}</td>
                                        <td>
                                            @if($invoice->status == 'paid')
                                                <span class="badge bg-success">Paid</span>
                                            @else
                                                <span class="badge bg-warning text-dark">Unpaid</span>
                                            @endif
                                        </td>
                                        <td>{{ number_format($invoice->grand_total, 2) }}</td>
                                        <td>
                                            <a href="{{ route('invoice.show', $invoice->id) }}" class="btn btn-sm btn-info">View</a>
                                            <form action="{{ route('invoice.destroy', $invoice->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this invoice?');">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div class="mt-3">
                            {{ $invoices->links() }} <!-- Laravel Pagination -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
