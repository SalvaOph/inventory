<?php

namespace App\Http\Controllers;

use App\Models\Provider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProviderController extends Controller
{
    public function index(Request $request)
    {
        $itemsPerPage = $request->get('items', 5);

        $sortBy = $request->get('sort_by', 'name');
        $sortOrder = $request->get('sort_order', 'asc'); 
        
        //Filtrar proveedores por search bar
        $query = $request->input('search');
        $providers = Provider::query();

        if ($query) {
            $providers->where('name', 'like', "%{$query}%")
                ->orWhere('address', 'like', "%{$query}%")
                ->orWhere('telephone', 'like', "%{$query}%")
                ->orWhere('email', 'like', "%{$query}%");
        }

        $providers = $providers->orderBy($sortBy, $sortOrder)
        ->paginate($itemsPerPage)
        ->appends([
            'search' => $query,
            'sort_by' => $sortBy,
            'sort_order' => $sortOrder,
            'items' => $itemsPerPage
        ]);

        return view('providers.index', compact('providers', 'itemsPerPage', 'query'));
    }

    public function create()
    {
        return view('providers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'address' => 'required',
            'telephone' => 'required',
            'email' => 'required',
        ]);

        Provider::create([
            'name' => $request->name,
            'address' => $request->address,
            'telephone' => $request->telephone,
            'email' => $request->email,
            'creator_user_id' => Auth::id(),
        ]);

        return redirect()->route('providers.index')
                         ->with('success', 'Provider created successfully.');
    }

    public function show(Provider $provider)
    {
        return view('providers.show', compact('provider'));
    }

    public function edit(Provider $provider)
    {
        return view('providers.edit', compact('provider'));
    }

    public function update(Request $request, Provider $provider)
    {
        $request->validate([
            'name' => 'required',
            'address' => 'required',
            'telephone' => 'required',
            'email' => 'required',
        ]);

        $updateData = $request->all();
        $updateData['last_update_user_id'] = auth()->id();

        $provider->update($updateData);

        return redirect()->route('providers.index')
                         ->with('success', 'Provider updated successfully.');
    }

    public function destroy(Provider $provider)
    {
        $provider->last_update_user_id = auth()->id();
        $provider->save();
        
        try {
            if($provider->delete()) {
            return response()->json(['status' => 'success', 'message' => 'Success! Provider deleted successfully.'], 200);
        } else {
            return response()->json(['status' => 'error', 'message' => 'The provider could not be deleted.'], 400);
        }
        }catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'The provider could not be deleted.'], 500);
        } 
    }
}
