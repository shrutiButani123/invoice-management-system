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
    <!-- Wrapper -->
    <div class="container my-5">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-lg rounded-lg">
                    <div class="card-header">
                        <h3 class="card-title">Users {{ auth()->user()->name}}</h3>
                    <!-- /.card-header -->
                    <div class="card-body no-pad-left-right">
                        <div class="table-responsive">
                            <table id="user_table" class="table table-bordered data-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Created At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($users as $user)
                                        <tr>
                                            <td class="border px-4 py-2">{{ $user->id }}</td>
                                            <td class="border px-4 py-2">{{ $user->name }}</td>
                                            <td class="border px-4 py-2">{{ $user->email }}</td>
                                            <td class="border px-4 py-2">{{ $user->created_at->format('d-m-Y H:i') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </div>
    
@endsection

@section('scripts')
    <!-- Include DataTables JS -->
    <script>
        $(document).ready(function() {
            $('#user_table').DataTable(); // Initialize DataTables
        });
    </script>
@endsection