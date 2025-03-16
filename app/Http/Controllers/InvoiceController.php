<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Item;
use App\Models\InvoiceItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Yajra\DataTables\Facades\DataTables;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $invoices = Invoice::where('user_id', Auth::id())->with('invoiceItems')->orderBy('created_at', 'desc');

            if (!empty($request->invoice_id)) {
                $invoices->where('id', $request->invoice_id);
            }
    
            if (!empty($request->date_range)) {
                $dates = explode(' to ', $request->date_range);
                if (count($dates) == 2) {
                    $startDate = \Carbon\Carbon::parse($dates[0])->startOfDay();
                    $endDate = \Carbon\Carbon::parse($dates[1])->endOfDay();
                    $invoices->whereBetween('invoice_date', [$startDate, $endDate]);
                }
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
                ->addColumn('actions', function ($invoice) {
                    return '
                        <a href="'.route('invoice.show', $invoice->id).'" class="btn btn-sm btn-info">View</a>
                        <form action="'.route('invoice.destroy', $invoice->id).'" method="POST" class="d-inline" onsubmit="return confirm(\'Are you sure you want to delete this invoice?\');">
                            '.csrf_field().'
                            '.method_field('DELETE').'
                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                        </form>';
                })
                ->rawColumns(['status', 'actions'])
                ->make(true);
        }
    
        return view('invoice.index');
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $items = Item::get();
        return view('invoice.create', compact('items'));
    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'customer_name' => 'required|string',
            'invoice_due_date' => 'required|date|after_or_equal:invoice_date',
            'invoice_date' => 'required|date',
            'invoice_status' => 'required|in:paid,unpaid',
            'final_subtotal' => 'required|numeric|min:0',
            'final_tax' => 'required|numeric|min:0',
            'final_discount' => 'nullable|numeric|min:0',
            'final_grand_total' => 'required|numeric|min:0',
            'item_name.*' => 'required|exists:items,id',
            'quantity.*' => 'required|integer|min:1',
            'amount.*' => 'required|numeric|min:0',
            'tax.*' => 'required|numeric|min:0',
            'grand_total.*' => 'required|numeric|min:0',
        ]);

        if($validator->fails()){
            return redirect()->back()->withErrors($validator)->withInput();
        }
 
        
        DB::beginTransaction();
        
        try {
            $invoice = new Invoice();
            $invoice->user_id = Auth::id();
            $invoice->customer_name = $request->customer_name;
            $invoice->description = $request->description;
            $invoice->due_date = $request->invoice_due_date;
            $invoice->invoice_date = $request->invoice_date;
            $invoice->total_amount = $request->final_subtotal;
            $invoice->total_tax = $request->final_tax;
            $invoice->grand_total = $request->final_grand_total;
            $invoice->status = $request->invoice_status;
            $invoice->save();

            foreach ($request->item_name as $index => $itemId) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'item_id' => $itemId,
                    'quantity' => $request->quantity[$index],
                    'price' => $request->amount[$index],
                    'tax' => $request->tax[$index],
                    'subtotal' => $request->grand_total[$index],
                ]);
            }
        
            DB::commit();
            return redirect()->route('invoices')->with('success', 'Invoice created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
            return redirect()->back()->with('error', 'Something went wrong!')->withInput();
        }            
    }

    public function show($id)
    {
        $invoice = Invoice::with('invoiceItems')->findOrFail($id);
        return view('invoice.show', compact('invoice'));
    }

    public function printPDF(Invoice $request){
        $invoicePdf = $this->generatePdf($request);
        return view('invoice.show')->with('success', 'Invoice Pdf created successfully!');
    }

    public function generatePdf($id){
         $invoice = Invoice::with('invoiceItems')->findOrFail($id);
         $pdf = Pdf::loadView('invoice.pdf', compact('invoice'));
         return $pdf->download("invoice_{$invoice->id}.pdf");
    }


    public function edit($id)
    {
        $invoice = Invoice::with('invoiceItems')->findOrFail($id);
        $items = Item::all();

        return view('invoice.edit', compact('invoice', 'items'));
    }

    public function update(Request $request, $id)
    {


        $validator = Validator::make($request->all(), [
            'customer_name' => 'required|string',
            'invoice_due_date' => 'required|date|after_or_equal:invoice_date',
            'invoice_date' => 'required|date',
            'invoice_status' => 'required|in:paid,unpaid',
            'final_subtotal' => 'required|numeric|min:0',
            'final_tax' => 'required|numeric|min:0',
            'final_discount' => 'nullable|numeric|min:0',
            'final_grand_total' => 'required|numeric|min:0',
            'item_name.*' => 'required|exists:items,id',
            'quantity.*' => 'required|integer|min:1',
            'amount.*' => 'required|numeric|min:0',
            'tax.*' => 'required|numeric|min:0',
            'grand_total.*' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $invoice = Invoice::findOrFail($id);
            $invoice->customer_name = $request->customer_name;
            $invoice->description = $request->description;
            $invoice->due_date = $request->invoice_due_date;
            $invoice->invoice_date = $request->invoice_date;
            $invoice->total_amount = $request->final_subtotal;
            $invoice->total_tax = $request->final_tax;
            $invoice->grand_total = $request->final_grand_total;
            $invoice->status = $request->invoice_status;
            $invoice->save();

            // Remove old items and add new ones
            InvoiceItem::where('invoice_id', $id)->delete();

            foreach ($request->item_name as $index => $itemId) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'item_id' => $itemId,
                    'quantity' => $request->quantity[$index],
                    'price' => $request->amount[$index],
                    'tax' => $request->tax[$index],
                    'subtotal' => $request->grand_total[$index],
                ]);
            }

            DB::commit();
            return redirect()->route('invoices')->with('success', 'Invoice updated successfully!');
        } catch (\Exception $e) {
            dd($e);
            DB::rollBack();
            return redirect()->back()->with('error', 'Something went wrong!')->withInput();
        }
    }

    public function destroy($id)
    {
        $invoice = Invoice::findOrFail($id);
        $invoice->delete();

        return redirect()->route('invoices')->with('success', 'Invoice deleted successfully.');
    }
}
