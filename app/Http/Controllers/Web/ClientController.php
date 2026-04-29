<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\StoreClientRequest;
use App\Http\Requests\Client\UpdateClientRequest;
use App\Models\Client;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $search = request('search');

        $clients = Client::query()
            ->when($search, fn ($query) => $query
                ->where(fn ($subQuery) => $subQuery
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")))
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Clients/Index', [
            'filters' => ['search' => $search],
            'clients' => $clients,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('Clients/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreClientRequest $request): RedirectResponse
    {
        Client::create($request->validated());

        return redirect()
            ->route('clients.index')
            ->with('success', 'Client created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Client $client): Response
    {
        return Inertia::render('Clients/Edit', [
            'client' => $client,
            'readonly' => true,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Client $client): Response
    {
        return Inertia::render('Clients/Edit', [
            'client' => $client,
            'readonly' => false,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateClientRequest $request, Client $client): RedirectResponse
    {
        $client->update($request->validated());

        return redirect()
            ->route('clients.index')
            ->with('success', 'Client updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client $client): RedirectResponse
    {
        $client->delete();

        return redirect()
            ->route('clients.index')
            ->with('success', 'Client archived successfully.');
    }
}
