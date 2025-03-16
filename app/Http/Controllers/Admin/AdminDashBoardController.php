<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use App\Models\User;
use App\Models\Item;
use App\Models\Invoice;
use App\Models\InvoiceItem;

class AdminDashBoardController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('admin.dashboard', compact('users'));
    }

    public function invoices(Request $request)
    {
        if ($request->ajax()) {
            $invoices = Invoice::with('invoiceItems')->orderBy('created_at', 'desc');
        
            if (!empty($request->user_id)) {
                $invoices->where('user_id', $request->user_id);
            }
        
            if (!empty($request->date_range)) {
                $dates = explode(' to ', $request->date_range);
                if (count($dates) == 2) {
                    $startDate = \Carbon\Carbon::parse($dates[0])->startOfDay();
                    $endDate = \Carbon\Carbon::parse($dates[1])->endOfDay();
                    $invoices->whereBetween('invoice_date', [$startDate, $endDate]);
                }
            }
        
            if ($invoices->count() == 0) {
                return response()->json(['error' => 'No invoices found'], 404);
            }
        
            return DataTables::of($invoices)
                ->addIndexColumn()
                ->editColumn('invoice_date', function ($invoice) {
                    return \Carbon\Carbon::parse($invoice->invoice_date)->format('d M Y');
                })
                ->editColumn('due_date', function ($invoice) {
                    return \Carbon\Carbon::parse($invoice->due_date)->format('d M Y');
                })
                ->editColumn('status', function ($invoice) {
                    return $invoice->status == 'paid' 
                        ? '<span class="badge bg-success">Paid</span>' 
                        : '<span class="badge bg-warning text-dark">Unpaid</span>';
                })
                ->editColumn('grand_total', function ($invoice) {
                    return number_format($invoice->grand_total, 2);
                })
                ->rawColumns(['status'])
                ->make(true);
        }
        
        $users = User::where('role', 'user')->get();
        return view('admin.invoice', compact('users'));
    }
}
