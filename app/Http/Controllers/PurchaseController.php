<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\Provider;
use App\Models\Purchase;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $itemsPerPage = $request->get('items', 5);

        $sortBy = $request->get('sort_by', 'total');
        $sortOrder = $request->get('sort_order', 'asc');

        //Filtrar compras por search bar
        $query = $request->input('search');
        $purchases = Purchase::with(['provider']);

        if ($query) {
            $purchases->where('total', 'like', "%{$query}%")
            ->orWhereHas('provider', function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%");
            });
        }

        $purchases = $purchases->orderBy($sortBy, $sortOrder)
        ->paginate($itemsPerPage)
        ->appends([
            'search' => $query,
            'sort_by' => $sortBy,
            'sort_order' => $sortOrder,
            'items' => $itemsPerPage
        ]);

        return view('purchases.index', compact('purchases', 'itemsPerPage', 'query'));
    }

    public function create()
    {
        $providers = Provider::all();
        $products = Product::all();
        $warehouses = Warehouse::all();
        return view('purchases.create', compact('providers', 'products', 'warehouses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required',
            'total' => 'required|numeric',
            'provider_id' => 'required|exists:providers,id',
            'warehouse_id' => 'required|exists:warehouses,id', // Validación del warehouse
            'products_id' => 'required|array',
            'products_quantity' => 'required|array',
            'products_id.*' => 'exists:products,id',
            'products_quantity.*' => 'numeric|min:1',
        ]);

        // Crear la compra
        DB::beginTransaction();

        try {
            // Crear la compra
            $purchase = Purchase::create([
                'date' => $request->date,
                'total' => $request->total,
                'provider_id' => $request->provider_id,
                'warehouse_id' => $request->warehouse_id,
                'creator_user_id' => Auth::id(),
            ]);

            // Combinamos los productos con sus cantidades
            $productsWithQuantities = array_combine($request->products_id, $request->products_quantity);

            // Agregar productos con cantidades a la tabla pivot
            foreach ($productsWithQuantities as $productId => $quantity) {
                $purchase->products()->attach($productId, ['quantity' => $quantity]);
            }

            // Actualizar inventario en el almacén seleccionado
            foreach ($productsWithQuantities as $productId => $quantity) {
                app(InventoryController::class)->updateInventory($productId, $request->warehouse_id, $quantity, 'purchase');
            }

            DB::commit();
            return redirect()->route('purchases.index')->with('success', 'Purchase created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function show(Purchase $purchase)
    {
        $products = $purchase->products()->withPivot('quantity')->get();
        $warehouse = Warehouse::find($purchase->warehouse_id);
        return view('purchases.show', compact('purchase', 'products', 'warehouse'));
    }

    public function edit(Purchase $purchase)
    {
        $providers = Provider::all();
        $products = $purchase->products()->withPivot('quantity')->get();
        $all_products = Product::all();
        $warehouses = Warehouse::all();
        return view('purchases.edit', compact('purchase', 'products', 'providers', 'all_products', 'warehouses'));
    }

    public function update(Request $request, Purchase $purchase)
    {
        $request->validate([
            'date' => 'required',
            'total' => 'required|numeric',
            'provider_id' => 'required|exists:providers,id',
            'warehouse_id' => 'required|exists:warehouses,id', // Validación del warehouse
            'products_id' => 'required|array',
            'products_quantity' => 'required|array',
            'products_id.*' => 'exists:products,id',
            'products_quantity.*' => 'numeric|min:1',
        ]);

        // Iniciar la transacción para garantizar la consistencia
        DB::beginTransaction();

        try {
            // Actualizar la compra
            $purchase->update(array_merge(
                $request->only('date', 'total', 'provider_id', 'warehouse_id'), 
                ['last_update_user_id' => Auth::id()]
            ));

            // Combinamos los productos con sus cantidades
            $productsWithQuantities = array_combine($request->products_id, $request->products_quantity);

            // Obtener los productos actuales de la compra y sus cantidades previas
            $currentProducts = $purchase->products()->withPivot('quantity')->get();

            // Eliminar las unidades previas de los productos en el inventario antes de actualizar
            foreach ($currentProducts as $product) {
                $oldQuantity = $product->pivot->quantity;

                // Eliminar las unidades previas del inventario (salida del inventario)
                app(InventoryController::class)->updateInventory($product->id, $purchase->warehouse_id, $oldQuantity, 'void', auth()->id());
            }

            // Eliminar los productos que ya no están en la solicitud
            foreach ($currentProducts as $product) {
                if (!in_array($product->id, $request->products_id)) {
                    $purchase->products()->detach($product->id);
                }
            }

            // Agregar o actualizar productos con cantidades a la tabla pivot
            foreach ($productsWithQuantities as $productId => $quantity) {
                if ($purchase->products()->where('id', $productId)->exists()) {
                    // Actualizar las cantidades en la tabla pivot
                    $purchase->products()->updateExistingPivot($productId, ['quantity' => $quantity]);
                } else {
                    // Agregar productos nuevos a la tabla pivot
                    $purchase->products()->attach($productId, ['quantity' => $quantity]);
                }

                // Si el almacén ha cambiado, debemos actualizar o crear inventario en el nuevo almacén
                if ($purchase->warehouse_id != $request->warehouse_id) {
                    // Si se ha cambiado el almacén, actualizar el inventario del nuevo almacén
                    app(InventoryController::class)->updateInventory($productId, $request->warehouse_id, $quantity, 'purchase', auth()->id());
                } else {
                    // Si no se ha cambiado el almacén, simplemente actualizamos el inventario del almacén original
                    app(InventoryController::class)->updateInventory($productId, $purchase->warehouse_id, $quantity, 'purchase', auth()->id() );
                }
            }

            // Confirmar los cambios si todo es exitoso
            DB::commit();
            return redirect()->route('purchases.index')->with('success', 'Purchase updated successfully.');
        } catch (\Exception $e) {
            // Deshacer los cambios si ocurre algún error
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function destroy(Purchase $purchase)
    {
        // Obtener los productos actuales de la compra y sus cantidades previas
        $currentProducts = $purchase->products()->withPivot('quantity')->get();

        // Eliminar las unidades previas de los productos en el inventario antes de actualizar
        foreach ($currentProducts as $product) {
            $oldQuantity = $product->pivot->quantity;

            // Eliminar las unidades previas del inventario (salida del inventario)
            app(InventoryController::class)->updateInventory($product->id, $purchase->warehouse_id, $oldQuantity, 'void', auth()->id());
        }

        $purchase->last_update_user_id = auth()->id();
        $purchase->save();

        try {
            if($purchase->delete()) {
            return response()->json(['status' => 'success', 'message' => 'Success! Purchase deleted successfully.'], 200);
        } else {
            return response()->json(['status' => 'error', 'message' => 'The purchase could not be deleted.'], 400);
        }
        }catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'The purchase could not be deleted.'], 500);
        } 
    }
}
