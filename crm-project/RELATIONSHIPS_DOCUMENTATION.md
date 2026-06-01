# CRM Relationships & Inertia Data Props Architecture

This document outlines the One-to-Many relationships established between the `Customer` model and the `Invoice` & `Proposal` models, details the use of Eager Loading to eliminate the N+1 query problem, and provides examples of how the nested collections are consumed reactively in the Inertia/Vue 3 frontend.

---

## 1. Relational Database Schema Diagram

Each **Customer** can have multiple **Invoices** and multiple **Proposals**. This is a standard **One-to-Many (1:N)** relationship.

```text
  ┌─────────────────┐
  │    customers    │
  ├─────────────────┤
  │ id (PK)         │◄────────────────────────┐
  │ name            │                         │
  │ email           │                         │
  │ phone           │                         │
  │ status          │                         │
  └────────┬────────┘                         │
           │                                  │
           │ 1:N (One-to-Many)                │ 1:N (One-to-Many)
           ├────────────────────────┐         │
           ▼                        ▼         │
  ┌─────────────────┐      ┌──────────────────┴─┐
  │    invoices     │      │     proposals      │
  ├─────────────────┤      ├────────────────────┤
  │ id (PK)         │      │ id (PK)            │
  │ customer_id (FK)│      │ customer_id (FK)   │
  │ invoice_number  │      │ title              │
  │ amount          │      │ description        │
  │ status          │      │ value              │
  │ due_date        │      │ status             │
  └─────────────────┘      └────────────────────┘
```

---

## 2. Eloquent Model Relationships

### A. Customer Model (`Customer.php`)
Defined in [Customer.php](file:///Users/isulailleperuma/Desktop/Weblook/CRM---Laravel-Project/crm-project/app/Models/Customer.php). A customer owns many proposals and invoices.

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'status',
    ];

    /**
     * Get all of the proposals for the customer.
     */
    public function proposals()
    {
        return $this->hasMany(Proposal::class);
    }

    /**
     * Get all of the invoices for the customer.
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
}
```

### B. Invoice Model (`Invoice.php`)
Defined in [Invoice.php](file:///Users/isulailleperuma/Desktop/Weblook/CRM---Laravel-Project/crm-project/app/Models/Invoice.php). Each invoice belongs to a single customer.

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'invoice_number',
        'amount',
        'status',
        'due_date'
    ];

    /**
     * Get the customer that owns the invoice.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
```

### C. Proposal Model (`Proposal.php`)
Defined in [Proposal.php](file:///Users/isulailleperuma/Desktop/Weblook/CRM---Laravel-Project/crm-project/app/Models/Proposal.php). Each proposal belongs to a single customer.

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proposal extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'title',
        'description',
        'value',
        'status'
    ];

    /**
     * Get the customer that owns the proposal.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
```

---

## 3. Eager Loading vs. N+1 Queries

### The N+1 Query Problem
When rendering a list of customers and displaying their associated invoices and proposals, a naive implementation might do this:

```php
// Naive implementation causing N+1
$customers = Customer::latest()->get(); // 1 query to fetch customers
```

In the blade or Vue frontend, iterating over each customer to display their details would run:
- A query to fetch invoices for Customer 1
- A query to fetch proposals for Customer 1
- A query to fetch invoices for Customer 2
- A query to fetch proposals for Customer 2
- ... and so on for **N** customers.

This results in **1 + 2N** database queries, which severely degrades application performance as the customer base scales.

### The Eager Loading Solution
By using Eloquent's `with` method, we declare the relationships to load upfront in **only 3 queries total** (1 for customers, 1 for all related invoices, and 1 for all related proposals):

```php
// Refactored in CustomerController.php
$customers = Customer::with(['invoices', 'proposals'])->latest()->get();
```

Laravel executes these queries linearly:
1. `SELECT * FROM customers ORDER BY created_at DESC;`
2. `SELECT * FROM invoices WHERE customer_id IN (...list of customer IDs...);`
3. `SELECT * FROM proposals WHERE customer_id IN (...list of customer IDs...);`

It then maps the collections in memory, providing a unified nested array structure to the Inertia response:

---

## 4. Consuming Nested Relationship Props in Vue 3

Below is an example of how the nested relationships are passed down via Inertia and consumed reactively in the Vue 3 frontend using `<script setup>`:

```vue
<script setup>
import { computed } from 'vue';

// Define the incoming props, including nested array structures
const props = defineProps({
    customers: {
        type: Array,
        required: true
    }
});

// Example computed property calculating total billing values
const totalOutstandingInvoices = computed(() => {
    return props.customers.reduce((sum, customer) => {
        const unpaidAmount = customer.invoices
            .filter(inv => inv.status === 'unpaid' || inv.status === 'overdue')
            .reduce((subtotal, inv) => subtotal + parseFloat(inv.amount), 0);
        return sum + unpaidAmount;
    }, 0);
});
</script>

<template>
    <div class="p-6">
        <h3 class="text-lg font-bold">Client Portfolios</h3>
        
        <div v-for="customer in customers" :key="customer.id" class="border p-4 rounded-xl mb-4 bg-white">
            <h4 class="font-bold text-gray-900">{{ customer.name }}</h4>
            <p class="text-xs text-gray-500">{{ customer.email }}</p>
            
            <!-- Displaying nested Invoices count & details -->
            <div class="mt-3">
                <span class="text-xs font-semibold uppercase text-gray-400">Invoices ({{ customer.invoices.length }})</span>
                <ul class="text-xs mt-1 space-y-1">
                    <li v-for="invoice in customer.invoices" :key="invoice.id" class="flex justify-between">
                        <span>{{ invoice.invoice_number }}</span>
                        <span class="font-semibold">{{ invoice.amount }} ({{ invoice.status }})</span>
                    </li>
                </ul>
            </div>

            <!-- Displaying nested Proposals count & details -->
            <div class="mt-3 border-t pt-2">
                <span class="text-xs font-semibold uppercase text-gray-400">Proposals ({{ customer.proposals.length }})</span>
                <ul class="text-xs mt-1 space-y-1">
                    <li v-for="proposal in customer.proposals" :key="proposal.id" class="flex justify-between">
                        <span>{{ proposal.title }}</span>
                        <span class="font-semibold">${{ proposal.value }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>
```
