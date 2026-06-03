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

    public function edit(Invoice $invoice)
    {
        $targetRoute = request()->input('redirect_to') === 'board' ? 'invoices.board' : 'invoices.index';

        if ($invoice->status === 'paid') {
            return redirect()->route($targetRoute)->with('error', 'Paid invoices cannot be edited.');
        }

        $customers = Customer::all();

        return Inertia::render('Invoices/Edit', [
            'invoice' => $invoice,
            'customers' => $customers
        ]);
    }

    public function update(Request $request, Invoice $invoice)
    {
        $targetRoute = $request->input('redirect_to') === 'board' ? 'invoices.board' : 'invoices.index';

        if ($invoice->status === 'paid') {
            return redirect()->route($targetRoute)->with('error', 'Paid invoices cannot be updated.');
        }

        $request->merge([
            'invoice_number' => strip_tags(trim($request->input('invoice_number'))),
        ]);

        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'invoice_number' => 'required|string|max:255|unique:invoices,invoice_number,' . $invoice->id,
            'amount' => 'required|numeric|min:0',
            'status' => 'required|string|in:unpaid,paid,overdue',
            'due_date' => 'required|date',
        ]);

        $oldStatus = $invoice->status;
        $invoice->update($validated);

        if ($validated['status'] === 'paid' && $oldStatus !== 'paid') {
            \App\Models\Transaction::create([
                'invoice_id' => $invoice->id,
                'stripe_session_id' => 'MANUAL_PAYMENT_' . strtoupper(uniqid()),
                'amount_paid' => $invoice->amount,
                'currency' => 'LKR',
                'payment_status' => 'completed'
            ]);
        }

        return redirect()->route($targetRoute)->with('success', 'Invoice updated successfully.');
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->delete();

        $targetRoute = request()->input('redirect_to') === 'board' ? 'invoices.board' : 'invoices.index';

        return redirect()->route($targetRoute);
    }

    public function board()
    {
        $invoices = Invoice::with('customer')->latest()->get();

        return Inertia::render('Invoices/InvoiceBoard', [
            'invoices' => $invoices
        ]);
    }
}
