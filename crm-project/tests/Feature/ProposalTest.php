<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Customer;
use App\Models\Proposal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProposalTest extends TestCase
{
    use RefreshDatabase;

    public function test_proposals_index_page_is_displayed_for_authenticated_users(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('proposals.index'));

        $response->assertOk();
    }

    public function test_guest_cannot_access_proposals_index(): void
    {
        $response = $this->get(route('proposals.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_proposals_create_page_is_displayed_for_authenticated_users(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('proposals.create'));

        $response->assertOk();
    }

    public function test_proposal_can_be_created(): void
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
            ->post(route('proposals.store'), [
                'customer_id' => $customer->id,
                'title' => 'Software Development Proposal',
                'description' => 'Scope of work for software dev project.',
                'value' => 12500.50,
                'status' => 'sent',
            ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('proposals.index'));

        $this->assertDatabaseHas('proposals', [
            'customer_id' => $customer->id,
            'title' => 'Software Development Proposal',
            'description' => 'Scope of work for software dev project.',
            'value' => 12500.50,
            'status' => 'sent',
        ]);
    }

    public function test_proposal_can_be_deleted(): void
    {
        $user = User::factory()->create();
        $customer = Customer::create([
            'name' => 'Acme Corp',
            'email' => 'acme@example.com',
            'phone' => '+12345678',
            'status' => 'active',
        ]);
        
        $proposal = Proposal::create([
            'customer_id' => $customer->id,
            'title' => 'Temporary Quote',
            'description' => 'A temporary quote details.',
            'value' => 500.00,
            'status' => 'draft',
        ]);

        $response = $this
            ->actingAs($user)
            ->delete(route('proposals.destroy', $proposal->id));

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('proposals.index'));

        $this->assertDatabaseMissing('proposals', [
            'id' => $proposal->id,
        ]);
    }
}
