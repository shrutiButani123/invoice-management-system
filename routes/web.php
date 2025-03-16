<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\User\DashBoardController;
use App\Http\Controllers\Admin\AdminDashBoardController;
use App\Http\Controllers\InvoiceController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', [RegistrationController::class, 'create'])->name('registration.create')->middleware('guest'); 
Route::post('/', [RegistrationController::class, 'store'])->name('registration.store')->middleware('guest'); 

Route::get('login', [LoginController::class, 'create'])->name('login.create')->middleware('guest'); 
Route::post('login', [LoginController::class, 'store'])->name('login.store')->middleware('guest');
Route::post('logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth'); 

Route::middleware(['auth', 'role:user'])->group(function(){
    Route::get('dashboard', [DashBoardController::class, 'index'])->name('user.dashboard');
     
    // Manage Invoices
    Route::get('invoices', [InvoiceController::class, 'index'])->name('invoices');
    Route::get('invoices/create', [InvoiceController::class, 'create'])->name('invoice.create');
    Route::post('invoices', [InvoiceController::class, 'store'])->name('invoice.store');
    Route::get('invoices/{id}/edit', [InvoiceController::class, 'edit'])->name('invoice.edit');
    // Route::put('invoices/{invoice}', [InvoiceController::class, 'update'])->name('invoice.update');
    Route::post('invoices/{id}', [InvoiceController::class, 'destroy'])->name('invoice.destroy');
    Route::get('/invoices/{id}', [InvoiceController::class, 'show'])->name('invoice.show');
    Route::get('/invoices/pdf/{id}', [InvoiceController::class, 'generatePdf'])->name('invoice.pdf');
});



Route::middleware(['auth', 'role:admin'])->group(function(){  
    Route::get('admin/dashboard', [AdminDashBoardController::class, 'index'])->name('admin.dashboard');
    Route::get('admin/invoices', [AdminDashBoardController::class, 'invoices'])->name('admin.invoices');
});
