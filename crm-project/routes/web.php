<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ProposalController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\StripePaymentController;
use App\Http\Controllers\TransactionController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/invoices/{invoice}/send', [StripePaymentController::class, 'sendInvoiceEmail'])->name('invoices.send');
    Route::get('/payment/{invoice}/success', [StripePaymentController::class, 'paymentSuccess'])->name('payment.success');
    Route::get('/payment/{invoice}/cancel', [StripePaymentController::class, 'paymentCancel'])->name('payment.cancel');
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');

    Route::resource('customers', CustomerController::class)->except(['show']);
    Route::resource('proposals', ProposalController::class)->only(['index', 'create', 'store', 'destroy']);
    Route::resource('invoices', InvoiceController::class)->only(['index', 'create', 'store', 'destroy']);
});

require __DIR__.'/auth.php';
