<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Customer;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InvoiceController extends Controller
{
    public function index()
    {
        // Eager-load customer relationships
        $invoices = Invoice::with('customer')->latest()->get();

        return Inertia::render('Invoices/Index', [
            'invoices' => $invoices
        ]);
    }

    public function create()
    {
        // Fetch all customers for the dropdown menu
        $customers = Customer::all();

        return Inertia::render('Invoices/Create', [
            'customers' => $customers
        ]);
    }

    public function store(Request $request)
    {
        $request->merge([
            'invoice_number' => strip_tags(trim($request->input('invoice_number'))),
        ]);

        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'invoice_number' => 'required|string|max:255|unique:invoices,invoice_number',
            'amount' => 'required|numeric|min:0',
            'status' => 'required|string|in:unpaid,paid,overdue',
            'due_date' => 'required|date',
        ]);

        Invoice::create($validated);

        return redirect()->route('invoices.index');
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->delete();

        return redirect()->route('invoices.index');
    }
}
