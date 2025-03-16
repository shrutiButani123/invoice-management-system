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

        <div class="flash-message">
            
        </div>

        <div class="row justify-content-center">
            <!-- Forms Start -->
            <div class="col-lg-10 mt-4">
                <div class="card rounded shadow">
                    <div class="p-4 border-bottom">
                        <form method="POST" action="{{ route('invoice.store') }}">
                            @csrf

                            <div class="row mt-4">
                                <!-- Customer Name -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Customer Name</label>
                                        <div class="">
                                            <input type="text" id="customer_name" name="customer_name" class="form-control @error('customer_name') is-invalid @enderror" value="{{ old('customer_name') }}" placeholder="Enter Customer Name">
                                            
                                            @error('customer_name')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div><!--end col-->

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Invoice Issue Date</label>
                                        <div class="">                                            
                                            <input name="invoice_date"  id="invoice_date" type="date" class="invoice_date form-control ps-5 @error('invoice_date') is-invalid @enderror"  value="{{ old('invoice_date') }}" placeholder="Invoice Issue Date">

                                            @error('invoice_date')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div> 
                                </div><!--end col-->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Invoice Due Date</label>
                                        <div class="">                                            
                                            <input name="invoice_due_date"  id="invoice_due_date" type="date" class="invoice_date form-control ps-5 @error('invoice_due_date') is-invalid @enderror"  value="{{ old('invoice_due_date') }}" placeholder="Invoice Due Date">

                                            @error('invoice_due_date')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div> 
                                </div><!--end col-->
                                <div class="col-md-6">
                                    <div class="mb-5">
                                        <label class="form-label">Invoice Status</label>
                                        <div class="">                        
                                            <select id="invoice_status" name="invoice_status"  class="form-select form-control @error('invoice_status') is-invalid @enderror" value="{{ old('invoice_status') }}" aria-label="Default select example">
                                                <option value="" >Select Invoice Status</option>
                                                <option  value="paid"   {{ old('invoice_status') == "paid" ? 'selected' : '' }}>Paid</option>
                                                <option  value="unpaid"  {{ old('invoice_status') == "unpaid" ? 'selected' : '' }}>Unpaid</option>
                                            </select>

                                            @error('invoice_status')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div><!--end col-->
                            </div>
                            <!-- Description -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Description</label>
                                    <div class="">                                    
                                        <textarea name="description" id="description" class="form-control ps-5 @error('description') is-invalid @enderror" rows="4" placeholder="Enter description here...">{{ old('description') }}</textarea>
                                        
                                        @error('description')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div><!--end col-->


                            <div class="row">
                                <div class="col-sm-12 d-flexd-grid gap-2 d-md-flex justify-content-md-end my-4">
                                    <button type="button" class="btn btn-primary" id="addrow_button">Add</button>
                                </div><!--end col-->
                            </div><!--end row-->

                            @if ($errors->has('items.*.item_name') || $errors->has('items.*.quantity') || $errors->has('items.*.amount') || $errors->has('items.*.tax'))
                                <div class="alert alert-danger">
                                    Please fill in all required item details correctly.
                                </div>
                                <div class="text-danger">Please fill in all required item details correctly.</div>
                            @endif

                            <table id="" class="display table table-striped table-hover" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>Items Name</th>
                                        <th>Quantity</th>
                                        <th>Amount</th>
                                        <th>Tax (%)</th>
                                        <th>Grand Total</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                    <tbody id="invoice_body">
                                    </tbody>
                            </table>
                            <div class="row mt-5">
                                <div class="col-lg-4 col-md-5 ms-auto">
                                    <ul class="list-unstyled h6 fw-normal mt-4 mb-0 ms-md-5 ms-lg-4">
                                        <li class="text-muted d-flex justify-content-between">Subtotal :<span class="final_subtotal"></span><input type="hidden" value="" name="final_subtotal" class="final_subtotal" required></li>
                                        <li class="text-muted d-flex justify-content-between">Vat :<span class="final_tax">0 </span><input type="hidden" value="" name="final_tax" class="final_tax" required></li>                                    
                                        <li class="d-flex justify-content-between" >Grand Total :<span class="final_grand_total"></span><input type="hidden" value="" name="final_grand_total" class="final_grand_total" required></li>
                                    </ul>
                                </div><!--end col-->
                            </div><!--end row--> 
                            <div class="row">
                                <div class="col-sm-12">
                                    <input type="submit" id="submit" name="send" class="btn btn-primary" value="Submit">
                                </div><!--end col-->
                            </div><!--end row-->
                        </form><!--end form-->   
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection


@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script type="text/javascript">
    

    var rowNo = 1;
    
    function addRow(rowNo) {
        var deleteAction = '';
        if(rowNo != 1 ){
            deleteAction = `<button class="delete-row form-control btn btn-primary" name="delete-row" data-id="${rowNo}">Remove</button>`;
        }
        var row = `<tr data-id="${rowNo}" class="item_row">
                        <td class="w-25"> 
                            <select class="item_class form-select form-control" name="item_name[]" data-id="${rowNo}" aria-label="Default select example required">
                                <option>Select item</option>
                                '@foreach ($items as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                '@endforeach
                            </select>
                        </td>
                        <td><input name="quantity[]" data-id="${rowNo}" type="number" class="quantity form-control"  value="" placeholder="Quantity" required></td>
                        <td><input name="amount[]" data-id="${rowNo}" type="number" class="amount form-control"  value="" placeholder="Amount" required></td>
                        <td><input name="tax[]"  data-id="${rowNo}" type="number" class="tax form-control"  value="" placeholder="Tax" required></td>
                        <td><input name="grand_total[]"  data-id="${rowNo}" type="text" class="grand_total form-control"  value="" placeholder="Grand Total" readonly required></td>
                        <td>${deleteAction}</td>
                    </tr>`;

        $('#invoice_body').append(row)
        // rowNo += 1
    }
       
    $(document).on('change', "select[name='item_name[]']", function(e) {
        e.preventDefault();
        var item_id = $(this).find('option:selected').val();
        var dataid = $(this).attr('data-id');
        // var dataid = $(this).data(id);

        $.ajax({
            type: 'GET',
            url: '/get-item/'+item_id,
            success: function(response) {
                // console.log(response);
                $('tr[data-id="'+dataid+'"] .quantity').val('1');
                // $('.quantity[data-id="'+dataid+'"]').val('1');
                // $('.amount[data-id="'+dataid+'"]').val(response.amount);
                // $('.tax[data-id="'+dataid+'"]').val(response.tax);
                // $('.grand_total[data-id="'+dataid+'"]').val(response.grand_total);
            }

        });
    });

    $('table').on('mouseup keyup', 'input[type=number]', () => calculateTotals());

    function calculateTotals() {
        const getAmount = $('.item_row').map((idx, val) => calculateSubtotal(val).amount).get();
        const amount = Number(getAmount.reduce((a, v) => a + Number(v), 0)).toFixed(2);

        const getTax = $('.item_row').map((idx, val) => calculateSubtotal(val).amountTax).get();
        const tax = Number(getTax.reduce((a, v) => a + Number(v), 0)).toFixed(2);

        const getTotal = $('.item_row').map((idx, val) => calculateSubtotal(val).total).get();
        const total = Number(getTotal.reduce((a, v) => a + Number(v), 0)).toFixed(2);


        $('.final_subtotal').html(amount);
        $('.final_subtotal').val(amount);

        $('.final_tax').html(tax);
        $('.final_tax').val(tax);

        $('.final_grand_total').html(total);
        $('.final_grand_total').val(total);


        
        
    }

    function calculateSubtotal(row) {
        const $row = $(row);
        const inputs = $row.find('input');
        // var static_amount = inputs[0].value;
        const amount = inputs[0].value * inputs[1].value;
        const taxPercentage = inputs[2].value;

        let amountTax = (amount + (amount * taxPercentage)/100);

        let tax = ((amount * taxPercentage)/100);
        $row.find('input[name="grand_total[]"]').val(amountTax);

        var itemData = {'amount': amount, 'amountTax': tax, 'total': amountTax};

        return itemData;
    }

    $('#invoice_body').on('click', '.delete-row', function () {
        $(this).closest('tr').remove();
        calculateTotals();
    });

    $(document).ready(function () {

        // $('#invoice').DataTable({});  
        // $('.invoice_date').flatpickr({});

        addRow(rowNo);

        $('#addrow_button').click(function(){
            rowNo += 1;
            addRow(rowNo);
        });

        // $(".item_class").select2({
        //     tags: true
        // }); 
        
    });

</script>
@endsection