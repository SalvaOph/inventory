<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $itemsPerPage = $request->get('items', 5);

        $sortBy = $request->get('sort_by', 'name');
        $sortOrder = $request->get('sort_order', 'asc'); 

        //Filtrar clientes por search bar
        $query = $request->input('search');
        $clients = Client::query();

        if ($query) {
            $clients->where('name', 'like', "%{$query}%")
                ->orWhere('address', 'like', "%{$query}%")
                ->orWhere('telephone', 'like', "%{$query}%")
                ->orWhere('email', 'like', "%{$query}%");
        }

        $clients = $clients->orderBy($sortBy, $sortOrder)
        ->paginate($itemsPerPage)
        ->appends([
            'search' => $query,
            'sort_by' => $sortBy,
            'sort_order' => $sortOrder,
            'items' => $itemsPerPage
        ]);
        
        return view('clients.index', compact('clients', 'itemsPerPage', 'query'));
    }

    public function create()
    {
        return view('clients.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'address' => 'required',
            'telephone' => 'required',
            'email' => 'required',
        ]);

        Client::create([
            'name' => $request->name,
            'address' => $request->address,
            'telephone' => $request->telephone,
            'email' => $request->email,
            'creator_user_id' => Auth::id(),
        ]);

        return redirect()->route('clients.index')
                         ->with('success', 'Client created successfully.');
    }

    public function show(Client $client)
    {
        return view('clients.show', compact('client'));
    }

    public function edit(Client $client)
    {
        return view('clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $request->validate([
            'name' => 'required',
            'address' => 'required',
            'telephone' => 'required',
            'email' => 'required',
        ]);

        $updateData = $request->all();
        $updateData['last_update_user_id'] = auth()->id();

        $client->update($updateData);

        return redirect()->route('clients.index')
                         ->with('success', 'Client updated successfully.');
    }

    public function destroy(Client $client)
    {
        $client->last_update_user_id = auth()->id();
        $client->save();

        try {
            if($client->delete()) {
            return response()->json(['status' => 'success', 'message' => 'Success! Client deleted successfully.'], 200);
        } else {
            return response()->json(['status' => 'error', 'message' => 'The client could not be deleted.'], 400);
        }
        }catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'The client could not be deleted.'], 500);
        } 
    }
}