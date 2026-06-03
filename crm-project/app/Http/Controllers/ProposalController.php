<?php

namespace App\Http\Controllers;

use App\Models\Proposal;
use App\Models\Customer;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProposalController extends Controller
{
    public function index()
    {
        // Fetch proposals and eager-load the parent customer relation object
        $proposals = Proposal::with('customer')->latest()->get();

        return Inertia::render('Proposals/Index', [
            'proposals' => $proposals
        ]);
    }

    public function create()
    {
        // Fetch active clients so the admin can choose one in the dropdown form
        $customers = Customer::all();

        return Inertia::render('Proposals/Create', [
            'customers' => $customers
        ]);
    }

    public function store(Request $request)
    {
        $request->merge([
            'title' => strip_tags(trim($request->input('title'))),
            'description' => strip_tags(trim($request->input('description'))),
        ]);

        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'value' => 'required|numeric|min:0',
            'status' => 'required|string|in:draft,sent,accepted,declined',
        ]);

        Proposal::create($validated);

        return redirect()->route('proposals.index');
    }

    public function edit(Proposal $proposal)
    {
        $customers = Customer::all();

        return Inertia::render('Proposals/Edit', [
            'proposal' => $proposal,
            'customers' => $customers
        ]);
    }

    public function update(Request $request, Proposal $proposal)
    {
        $request->merge([
            'title' => strip_tags(trim($request->input('title'))),
            'description' => strip_tags(trim($request->input('description'))),
        ]);

        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'value' => 'required|numeric|min:0',
            'status' => 'required|string|in:draft,sent,accepted,declined',
        ]);

        $proposal->update($validated);

        if ($request->input('redirect_to') === 'board') {
            return redirect()->route('proposals.board')->with('success', 'Proposal updated successfully.');
        }

        return redirect()->route('proposals.index')->with('success', 'Proposal updated successfully.');
    }

    public function destroy(Proposal $proposal)
    {
        $proposal->delete();

        if (request()->input('redirect_to') === 'board') {
            return redirect()->route('proposals.board')->with('success', 'Proposal deleted successfully.');
        }

        return redirect()->route('proposals.index');
    }

    public function board()
    {
        $proposals = Proposal::with('customer')->latest()->get();

        return Inertia::render('Proposals/PipelineBoard', [
            'proposals' => $proposals
        ]);
    }
}