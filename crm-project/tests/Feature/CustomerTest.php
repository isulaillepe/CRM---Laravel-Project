<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerTest extends TestCase
{
    use RefreshDatabase;

    public function test_customers_index_page_is_displayed_for_authenticated_users(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('customers.index'));

        $response->assertOk();
    }

    public function test_guest_cannot_access_customers_index(): void
    {
        $response = $this->get(route('customers.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_customer_can_be_created(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('customers.store'), [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'phone' => '+15551234567',
                'status' => 'active',
            ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('customers.index'));

        $this->assertDatabaseHas('customers', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '+15551234567',
            'status' => 'active',
        ]);
    }

    public function test_customer_can_be_updated(): void
    {
        $user = User::factory()->create();
        $customer = Customer::create([
            'name' => 'Old Name',
            'email' => 'old@example.com',
            'phone' => '+123',
            'status' => 'inactive',
        ]);

        $response = $this
            ->actingAs($user)
            ->patch(route('customers.update', $customer->id), [
                'name' => 'New Name',
                'email' => 'new@example.com',
                'phone' => '+456',
                'status' => 'active',
            ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('customers.index'));

        $this->assertDatabaseHas('customers', [
            'id' => $customer->id,
            'name' => 'New Name',
            'email' => 'new@example.com',
            'phone' => '+456',
            'status' => 'active',
        ]);
    }

    public function test_customer_can_be_deleted(): void
    {
        $user = User::factory()->create();
        $customer = Customer::create([
            'name' => 'Delete Customer',
            'email' => 'delete@example.com',
            'phone' => '+99999',
            'status' => 'active',
        ]);

        $response = $this
            ->actingAs($user)
            ->delete(route('customers.destroy', $customer->id));

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('customers.index'));

        $this->assertDatabaseMissing('customers', [
            'id' => $customer->id,
        ]);
    }
}
