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
                    <li class="nav-item"><a class="nav-link active" href="{{route('admin.dashboard')}}">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{route('admin.invoices')}}">Inovices</a></li>
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
                    <div class="p-4 border-bottom d-flex justify-content-between">
                        <h4 class="card-title">Invoice List</h4>
                    </div>

                    <!-- Search Filters -->
                    <div class="p-4">
                        <form id="search-form">
                            <div class="row">
                                <div class="col-md-4">
                                    <select id="user_filter" class="form-control">
                                        <option value="">All Users</option>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <input type="text" id="date_range" class="form-control" placeholder="Select Date Range">
                                </div>
                                <div class="col-md-4">
                                    <button type="button" id="search-button" class="btn btn-primary">Search</button>
                                    <button type="button" id="reset-button" class="btn btn-secondary">Reset</button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="table-responsive p-4">
                        <table class="table table-striped" id="admin_invoice_table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Invoice ID</th>
                                    <th>Customer Name</th>
                                    <th>Invoice Date</th>
                                    <th>Due Date</th>
                                    <th>Status</th>
                                    <th>Total Amount</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#date_range').daterangepicker({
            autoUpdateInput: false,
            locale: {
                cancelLabel: 'Clear'
            }
        });

        $('#date_range').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD') + ' to ' + picker.endDate.format('YYYY-MM-DD'));
        });

        $('#date_range').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });

        var table = $('#admin_invoice_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.invoices') }}",
                data: function(d) {
                    d.user_id = $('#user_filter').val();
                    d.date_range = $('#date_range').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'id', name: 'id' },
                { data: 'customer_name', name: 'customer_name' },
                { data: 'invoice_date', name: 'invoice_date' },
                { data: 'due_date', name: 'due_date' },
                { data: 'status', name: 'status' },
                { data: 'grand_total', name: 'grand_total' },
            ]
        });

        $('#search-button').click(function() {
            table.draw();
        });

        $('#reset-button').click(function() {
            $('#user_filter').val('');
            $('#date_range').val('');
            table.draw();
        });
    });
</script>
@endsection
