<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $itemsPerPage = $request->get('items', 5);

        //Filtrar inventarios por search bar
        $query = $request->input('search');

        $products = Product::select('products.*', DB::raw('MIN(warehouses.name) as warehouse_name'))
            ->leftJoin('inventories', 'products.id', '=', 'inventories.product_id')
            ->leftJoin('warehouses', 'inventories.warehouse_id', '=', 'warehouses.id')
            ->when($query, function ($q) use ($query) {
                $q->where('products.name', 'like', "%{$query}%");
            })
            ->groupBy('products.id')
            ->with(['inventories.warehouse'])
            ->paginate($itemsPerPage);

            $totalInventories = Inventory::select('product_id', DB::raw('SUM(stock) as total_stock'))
            ->groupBy('product_id')
            ->get()
            ->mapWithKeys(function ($inventory) {
                return [$inventory->product_id => $inventory->total_stock];
            });


        return view('inventories.index', compact('products', 'itemsPerPage', 'query', 'totalInventories'));
    }

    public function create()
    {
        $products = Product::all();
        $warehouses = Warehouse::all();
        return view('inventories.create', compact('warehouses', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|numeric|exists:products,id',
            'stock' => 'required|numeric|min:0',
            'warehouse_id' => 'required|numeric|exists:warehouses,id',
        ]);

        // Verificar si el inventario ya existe
        $exists = Inventory::where('product_id', $request->product_id)
            ->where('warehouse_id', $request->warehouse_id)
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->withErrors(['error' => 'The inventory for this product and warehouse already exists.'])
                ->withInput();
        }

        Inventory::create([
            'product_id' => $request->product_id,
            'stock' => $request->stock,
            'warehouse_id' => $request->warehouse_id,
            'creator_user_id' => Auth::id(),
        ]);

        return redirect()->route('inventories.index')
            ->with('success', 'Inventory created successfully.');
    }

    public function show(Inventory $inventory)
    {
        return view('inventories.show', compact('inventory'));
    }

    public function edit(Inventory $inventory)
    {
        $products = Product::all();
        $warehouses = Warehouse::all();
        return view('inventories.edit', compact('inventory', 'products', 'warehouses'));
    }

    public function update(Request $request, Inventory $inventory)
    {
        $request->validate([
            'product_id' => 'required|numeric',
            'stock' => 'required|numeric',
            'warehouse_id' => 'required',
        ]);

        $updateData = $request->all();
        $updateData['last_update_user_id'] = auth()->id();
        $inventory->update($updateData);

        return redirect()->route('inventories.index')
            ->with('success', 'Inventory updated successfully.');
    }

    public function destroy(Inventory $inventory)
    {
        $inventory->last_update_user_id = auth()->id();
        $inventory->save();

        try {
            if ($inventory->delete()) {
                return response()->json(['status' => 'success', 'message' => 'Success! Inventory deleted successfully.'], 200);
            } else {
                return response()->json(['status' => 'error', 'message' => 'The inventory could not be deleted.'], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'The inventory could not be deleted.'], 500);
        }
    }

    public function updateInventory($product_id, $warehouse_id, $quantity, $operation, $lastUpdateUserId = null)
    {
        // Uso de transacciones para asegurar la consistencia de datos
        DB::transaction(function () use ($product_id, $warehouse_id, $quantity, $operation, $lastUpdateUserId) {
            $inventory = Inventory::where('product_id', $product_id)
                ->where('warehouse_id', $warehouse_id)
                ->first();

            if (!$inventory) {
                // Crear inventario si no existe
                $inventory = new Inventory();
                $inventory->product_id = $product_id;
                $inventory->warehouse_id = $warehouse_id;
                $inventory->stock = 0; // Inicializar en 0
                $inventory->creator_user_id = Auth::id();
            }

            switch ($operation) {
                case 'sale':
                    if ($inventory->stock < $quantity) {
                        throw new \Exception('Insufficient stock in the selected warehouse.');
                    }
                    $inventory->stock -= $quantity;
                    break;

                case 'purchase':
                    $inventory->stock += $quantity;
                    break;

                case 'restock':
                    // Revertir el inventario añadiendo la cantidad previamente vendida
                    $inventory->stock += $quantity;
                    break;

                case 'void':
                    // Revertir el inventario añadiendo la cantidad previamente vendida
                    $inventory->stock -= $quantity;
                    break;

                default:
                    throw new \Exception('Invalid operation specified.');
            }

            if ($lastUpdateUserId) {
                $inventory->last_update_user_id = $lastUpdateUserId;
            }

            $inventory->save();
        });
    }
}
