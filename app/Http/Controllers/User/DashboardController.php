<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Invoice;

class DashboardController extends Controller
{
    public function index()
    {
        $totalRevenue = Invoice::where('user_id', Auth::id())->where('status', 'paid')->sum('grand_total');
        $outstandingInvoices = Invoice::where('user_id', Auth::id())->where('status', 'unpaid')->sum('grand_total');
        return view('user.dashboard', compact('totalRevenue', 'outstandingInvoices'));
    }
}
