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
use Konekt\PdfInvoice\InvoicePrinter;
use Illuminate\Support\Facades\File;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::with('invoiceItems')->orderBy('created_at', 'desc')->paginate(10);
    
        return view('invoice.index', compact('invoices'));
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
            $invoice->user_id = 1;
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

    public function generatePdf($req){
        $invoicePdf  = new InvoicePrinter();

        $invoicePdf->setColor('#007fff');
        $invoicePdf->setType('Invoice');
        $invoicePdf->setReference($req->id); 
        $invoicePdf->setDate(date('dS M,Y',time()));
        $invoicePdf->setDue(date('M dS ,Y',strtotime($req->invoice_due_date)));  
        $invoicePdf->setFrom(array("Invoice Management"));

        $customerData = Invoice::with('users')->where('user_id', 1)->first();

        $invoicePdf->setTo(array($customerData['customer_name'] ));

        $invoiceItems = InvoiceItem::where('invoice_id', Invoice::pluck('id')->last())->get();
        foreach ($invoiceItems as $invoiceItem) {
            $serviceName = null;
        
            if ($invoiceItem->services_name) {
                $serviceName = $invoiceItem->services_name;
            } else {
                $service = Item::where('id', $invoiceItem->item_id)->first();
                $serviceName = optional($service)->name ?? "Unknown Service"; // Handle null case
            }
        
            $invoicePdf->addItem(
                $serviceName,                  
                "",                               
                $invoiceItem->quantity,      
                $invoiceItem->tax . '%',   
                $invoiceItem->amount,        
                $invoiceItem->grand_total,  
                ""                               
            );
        }
        
        $invoiceData = Invoice::latest()->first();
        $invoicePdf->addTotal("Sub Total", $invoiceData->amount);
        $invoicePdf->addTotal("VAT", $invoiceData->tax);
        $invoicePdf->addTotal("Discount", $invoiceData->discount);
        $invoicePdf->addTotal("Grand Total", $invoiceData->grand_total,true);
        
        $invoicePdf->addBadge("Payment Paid");

        $invoicePdf->addTitle("Important Notice");
        
        $invoicePdf->addParagraph("No item will be replaced or refunded if you don't have the invoice with you.");

        $invoicePdf->addTitle("Bank Details");
        
        $invoicePdf->addParagraph("Bank Name:");
        $invoicePdf->addParagraph("Account Name:");
        $invoicePdf->addParagraph("Account Number:");
        $invoicePdf->addParagraph("Branch Name:");
        $invoicePdf->addParagraph("Branch Code:");
        $invoicePdf->addParagraph("Account Type:");
        $invoicePdf->addParagraph("Swift Code:");
        $invoicePdf->addParagraph("Reference for Payment:");

        $invoicePdf->setFooternote("Invoice Management");

        $path = public_path('invoice');
        if(!File::exists($path)) {
            File::makeDirectory($path);
            $pdfName = public_path('invoice').'\invoice_'.$req->invoice_number.'.pdf';
        }
        else {
            $pdfName = public_path('invoice').'\invoice_'.$req->invoice_number.'.pdf';
        }
       
        $savePdf =  $invoicePdf->render($pdfName,'F'); 
        return $pdfName;
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
