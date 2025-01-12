<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WarehouseController extends Controller
{
    public function index(Request $request)
    {
        $itemsPerPage = $request->get('items', 5);

        $sortBy = $request->get('sort_by', 'name');
        $sortOrder = $request->get('sort_order', 'asc'); 

        //Filtrar almacen por search bar
        $query = $request->input('search');
        $warehouses = Warehouse::query();

        if ($query) {
            $warehouses->where('name', 'like', "%{$query}%")
                ->orWhere('address', 'like', "%{$query}%")
                ->orWhere('telephone', 'like', "%{$query}%")
                ->orWhere('email', 'like', "%{$query}%");
        }

        $warehouses = $warehouses->orderBy($sortBy, $sortOrder)
        ->paginate($itemsPerPage)
        ->appends([
            'search' => $query,
            'sort_by' => $sortBy,
            'sort_order' => $sortOrder,
            'items' => $itemsPerPage
        ]);

        return view('warehouses.index', compact('warehouses', 'itemsPerPage', 'query'));
    }

    public function create()
    {
        return view('warehouses.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'address' => 'required',
            'telephone' => 'required',
            'email' => 'required',
        ]);

        Warehouse::create([
            'name' => $request->name,
            'address' => $request->address,
            'telephone' => $request->telephone,
            'email' => $request->email,
            'creator_user_id' => Auth::id(),
        ]);

        return redirect()->route('warehouses.index')
                         ->with('success', 'Warehouse created successfully.');
    }

    public function show(Warehouse $warehouse)
    {
        return view('warehouses.show', compact('warehouse'));
    }

    public function edit(Warehouse $warehouse)
    {
        return view('warehouses.edit', compact('warehouse'));
    }

    public function update(Request $request, Warehouse $warehouse)
    {
        $request->validate([
            'name' => 'required',
            'address' => 'required',
            'telephone' => 'required',
            'email' => 'required',
        ]);

        $updateData = $request->all();
        $updateData['last_update_user_id'] = auth()->id();

        $warehouse->update($updateData);

        return redirect()->route('warehouses.index')
                         ->with('success', 'Warehouse updated successfully.');
    }

    public function destroy(Warehouse $warehouse)
    {
        $warehouse->last_update_user_id = auth()->id();
        $warehouse->save();

        try {
            if($warehouse->delete()) {
            return response()->json(['status' => 'success', 'message' => 'Success! Warehouse deleted successfully.'], 200);
        } else {
            return response()->json(['status' => 'error', 'message' => 'The warehouse could not be deleted.'], 400);
        }
        }catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'The warehouse could not be deleted.'], 500);
        } 
    }
}

