<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
