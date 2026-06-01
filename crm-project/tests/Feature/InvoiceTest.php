<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Customer;
use App\Models\Invoice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_invoices_index_page_is_displayed_for_authenticated_users(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('invoices.index'));

        $response->assertOk();
    }

    public function test_guest_cannot_access_invoices_index(): void
    {
        $response = $this->get(route('invoices.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_invoices_create_page_is_displayed_for_authenticated_users(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('invoices.create'));

        $response->assertOk();
    }

    public function test_invoice_can_be_created(): void
    {
        $user = User::factory()->create();
        $customer = Customer::create([
            'name' => 'Acme Corp',
            'email' => 'acme@example.com',
            'phone' => '+12345678',
            'status' => 'active',
        ]);

        $response = $this
            ->actingAs($user)
            ->post(route('invoices.store'), [
                'customer_id' => $customer->id,
                'invoice_number' => 'INV-20260601-9999',
                'amount' => 1500.75,
                'status' => 'unpaid',
            ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('invoices.index'));

        $this->assertDatabaseHas('invoices', [
            'customer_id' => $customer->id,
            'invoice_number' => 'INV-20260601-9999',
            'amount' => 1500.75,
            'status' => 'unpaid',
        ]);
    }

    public function test_invoice_can_be_deleted(): void
    {
        $user = User::factory()->create();
        $customer = Customer::create([
            'name' => 'Acme Corp',
            'email' => 'acme@example.com',
            'phone' => '+12345678',
            'status' => 'active',
        ]);
        
        $invoice = Invoice::create([
            'customer_id' => $customer->id,
            'invoice_number' => 'INV-TEMP-1111',
            'amount' => 200.00,
            'status' => 'unpaid',
        ]);

        $response = $this
            ->actingAs($user)
            ->delete(route('invoices.destroy', $invoice->id));

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('invoices.index'));

        $this->assertDatabaseMissing('invoices', [
            'id' => $invoice->id,
        ]);
    }
}
