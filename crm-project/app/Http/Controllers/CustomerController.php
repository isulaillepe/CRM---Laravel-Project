<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CustomerController extends Controller
{
    public function index()
    {
        // 1. Fetch customers from MySQL using the Model
        $customers = Customer::latest()->get();

        // 2. Pass data straight to the Vue component inside resources/js/Pages/
        return inertia('Customers/Index', [
            'customers' => $customers
        ]);
    }
}