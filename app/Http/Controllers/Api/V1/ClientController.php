<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\StoreClientRequest;
use App\Http\Requests\Client\UpdateClientRequest;
use App\Http\Resources\ClientResource;
use App\Models\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
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

        return ClientResource::collection($clients);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreClientRequest $request): JsonResponse
    {
        $client = Client::create($request->validated());

        return (new ClientResource($client))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Client $client): ClientResource
    {
        return new ClientResource($client);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateClientRequest $request, Client $client): ClientResource
    {
        $client->update($request->validated());

        return new ClientResource($client->fresh());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client $client): JsonResponse
    {
        $client->delete();

        return response()->json([
            'message' => 'Client archived successfully.',
        ]);
    }
}
