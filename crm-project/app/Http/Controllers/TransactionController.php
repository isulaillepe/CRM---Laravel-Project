<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Inertia\Inertia;

class TransactionController extends Controller
{public function index()
    {
        return Inertia::render('Transactions/Index', [
            // Eager-loading the invoice, and the customer assigned to that invoice
            'transactions' => Transaction::with('invoice.customer')
                ->latest()
                ->get()
        ]);
    }
}
