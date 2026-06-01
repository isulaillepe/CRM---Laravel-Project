<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CustomerController extends Controller
{
    public function index()
    {
        // 1. Fetch customers with eager-loaded relations to avoid N+1 queries
        $customers = Customer::with(['invoices', 'proposals'])->latest()->get();

        // 2. Pass data straight to the Vue component inside resources/js/Pages/
        return inertia('Customers/Index', [
            'customers' => $customers
        ]);
    }
    public function create()
    {
        // Serve the Create.vue component we just built
        return Inertia::render('Customers/Create');
    }

    /**
     * Store a newly created customer in storage.
     */
    public function store(Request $request)
    {
        $request->merge([
            'name' => strip_tags(trim($request->input('name'))),
            'email' => filter_var(trim($request->input('email')), FILTER_SANITIZE_EMAIL),
            'phone' => $request->filled('phone') ? strip_tags(trim($request->input('phone'))) : null,
        ]);

        // 1. Validate incoming data payloads strictly
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:customers',
            'phone' => 'nullable|string|max:20',
            'status' => 'required|string|in:active,inactive',
        ]);

        // 2. Mass-assign valid data directly to MySQL via the Model
        Customer::create($validated);

        // 3. Linearly redirect the browser instance straight back to the index interface
        return redirect()->route('customers.index');
    }
    public function edit(Customer $customer)
    {
        // Serve the Edit form view and automatically pass the loaded customer object
        return Inertia::render('Customers/Edit', [
            'customer' => $customer
        ]);
    }

    /**
     * Update the specified customer in storage.
     */
    public function update(Request $request, Customer $customer)
    {
        $request->merge([
            'name' => strip_tags(trim($request->input('name'))),
            'email' => filter_var(trim($request->input('email')), FILTER_SANITIZE_EMAIL),
            'phone' => $request->filled('phone') ? strip_tags(trim($request->input('phone'))) : null,
        ]);

        // 1. Validate the payload (ensure unique rule ignores the current customer's ID)
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:customers,email,' . $customer->id,
            'phone' => 'nullable|string|max:20',
            'status' => 'required|string|in:active,inactive',
        ]);

        // 2. Perform the update linearly
        $customer->update($validated);

        // 3. Redirect back to the index layout
        return redirect()->route('customers.index');
    }

    /**
     * Remove the specified customer from storage.
     */
    public function destroy(Customer $customer)
    {
        // Delete the entry from MySQL
        $customer->delete();

        // Redirect back to refresh the data table array automatically
        return redirect()->route('customers.index');
    }
    

}