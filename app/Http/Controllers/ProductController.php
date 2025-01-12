<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $itemsPerPage = $request->get('items', 5);

        $sortBy = $request->get('sort_by', 'name');
        $sortOrder = $request->get('sort_order', 'asc');

        //Filtrar productos por search bar
        $query = $request->input('search');
        $products = Product::query();

        if ($query) {
            $products->where('name', 'like', "%{$query}%")
                ->orWhere('description', 'like', "%{$query}%");
        }

        $products = $products->orderBy($sortBy, $sortOrder)
        ->paginate($itemsPerPage)
        ->appends([
            'search' => $query,
            'sort_by' => $sortBy,
            'sort_order' => $sortOrder,
            'items' => $itemsPerPage
        ]);

        $inventories = Inventory::all();
        return view('products.index', compact('products', 'inventories', 'itemsPerPage', 'query'));
    }

    public function create()
    {
        $warehouses = Warehouse::all();
        return view('products.create', compact('warehouses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            // Validaciones del producto
            'name' => 'required',
            'description' => 'required',
            'price' => 'required|numeric',

            // Validaciones del inventario
            'stock' => 'required|numeric',
            'warehouse_id' => 'required',
        ]);

        // Crear el producto
        $product = Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'creator_user_id' => Auth::id(),
        ]);

        // Crear el inventario asociado al producto
        Inventory::create([
            'product_id' => $product->id,
            'stock' => $request->stock,
            'warehouse_id' => $request->warehouse_id,
            'creator_user_id' => Auth::id(),
        ]);

        return redirect()->route('products.index')
            ->with('success', 'Product and inventory created successfully.');
    }


    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'price' => 'required|numeric',
        ]);

        $updateData = $request->all();
        $updateData['last_update_user_id'] = auth()->id();

        $product->update($updateData);

        return redirect()->route('products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        $product->last_update_user_id = auth()->id();
        $product->save();

        try {
            if($product->delete()) {
            return response()->json(['status' => 'success', 'message' => 'Success! Product deleted successfully.'], 200);
        } else {
            return response()->json(['status' => 'error', 'message' => 'The product could not be deleted.'], 400);
        }
        }catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'The product could not be deleted.'], 500);
        } 
    }
}