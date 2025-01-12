<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $itemsPerPage = $request->get('items', 5);

        $sortBy = $request->get('sort_by', 'total');
        $sortOrder = $request->get('sort_order', 'asc');

        //Filtrar ventas por search bar
        $query = $request->input('search');
        $sales = Sale::with(['client']);

        if ($query) {
            $sales->where('total', 'like', "%{$query}%")
            ->orWhereHas('client', function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%");
            });
        }

        $sales = $sales->orderBy($sortBy, $sortOrder)
        ->paginate($itemsPerPage)
        ->appends([
            'search' => $query,
            'sort_by' => $sortBy,
            'sort_order' => $sortOrder,
            'items' => $itemsPerPage
        ]);

        return view('sales.index', compact('sales', 'itemsPerPage', 'query'));
    }

    public function create()
    {
        $clients = Client::all();
        $products = Product::all();
        $warehouses = Warehouse::all();
        return view('sales.create', compact('clients', 'products', 'warehouses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required',
            'total' => 'required|numeric',
            'client_id' => 'required|exists:clients,id',
            'products_id' => 'required|array',
            'products_quantity' => 'required|array',
            'products_id.*' => 'exists:products,id',
            'products_quantity.*' => 'numeric|min:1',
            'warehouse_id' => 'required|exists:warehouses,id',
        ]);


        // Comenzamos una transacción para la venta
        DB::beginTransaction();

        // Combinamos los productos con sus cantidades
        $productsWithQuantities = array_combine($request->products_id, $request->products_quantity);

        try {
            $errorMessages = [];

            // Verificar stock de todos los productos primero
            foreach ($productsWithQuantities as $productId => $quantity) {
                // Verificar si hay suficiente stock en el inventario
                $inventory = Inventory::where('product_id', $productId)
                    ->where('warehouse_id', $request->warehouse_id)
                    ->first();

                if (!$inventory || $inventory->stock < $quantity) {
                    $productName = Product::find($productId)->name;
                    $errorMessages[] = 'Insufficient stock in this warehouse for product: ' . $productName  . " || Inventory stock: " . $inventory->stock . " || Inventory ID: " . $inventory->id;
                }
            }

            // Si se encontraron productos con stock insuficiente, lanzar una excepción
            if (!empty($errorMessages)) {
                throw new \Exception(implode(' | ', $errorMessages));
            }

            // Crear la venta después de verificar stock
            $sale = Sale::create([
                'date' => $request->date,
                'total' => $request->total,
                'client_id' => $request->client_id,
                'warehouse_id' => $request->warehouse_id,
                'creator_user_id' => Auth::id(),
            ]);

            // Asociar productos a la venta en la tabla pivot y actualizar inventario
            foreach ($productsWithQuantities as $productId => $quantity) {
                // Asociar productos con cantidades a la venta
                $sale->products()->attach($productId, ['quantity' => $quantity]);

                // Actualizar inventario
                app(InventoryController::class)->updateInventory($productId, $request->warehouse_id, $quantity, 'sale', auth()->id());
            }

            // Confirmar transacción
            DB::commit();

            return redirect()->route('sales.index')->with('success', 'Sale created successfully.');
        } catch (\Exception $e) {
            // Si ocurre algún error, revertimos los cambios
            DB::rollBack();

            return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }



    public function show(Sale $sale)
    {
        $products = $sale->products()->withPivot('quantity')->get();
        $warehouse = Warehouse::find($sale->warehouse_id);
        return view('sales.show', compact('sale', 'products', 'warehouse'));
    }


    public function edit(Sale $sale)
    {
        $clients = Client::all();
        $products = $sale->products()->withPivot('quantity')->get();
        $warehouses = Warehouse::all();
        $all_products = Product::all();
        return view('sales.edit', compact('sale', 'products', 'clients', 'all_products', 'warehouses'));
    }

    public function update(Request $request, Sale $sale)
    {
        $request->validate([
            'date' => 'required',
            'total' => 'required|numeric',
            'client_id' => 'required|exists:clients,id',
            'products_id' => 'required|array',
            'products_quantity' => 'required|array',
            'products_id.*' => 'exists:products,id',
            'products_quantity.*' => 'numeric|min:1',
            'warehouse_id' => 'required|exists:warehouses,id',
        ]);

        DB::beginTransaction();

        try {

            // Combinamos los productos con sus cantidades
            $productsWithQuantities = array_combine($request->products_id, $request->products_quantity);

            // Revertir las cantidades previas en el inventario (restock)
            foreach ($sale->products as $product) {
                app(InventoryController::class)->updateInventory($product->id, $sale->warehouse_id, $product->pivot->quantity, 'restock', auth()->id());
            }


            // Actualizar la venta
            $sale->update([
                'date' => $request->date,
                'total' => $request->total,
                'client_id' => $request->client_id,
                'warehouse_id' => $request->warehouse_id,
                'last_update_user_id' => Auth::id(),
            ]);

            // Eliminar productos antiguos de la tabla pivot
            $sale->products()->detach();

            $errorMessages = [];

            // Verificar stock suficiente y actualizar el inventario con las nuevas cantidades
            foreach ($productsWithQuantities as $productId => $quantity) {
                // Verificar si hay suficiente stock para la nueva venta
                $inventory = Inventory::where('product_id', $productId)
                    ->where('warehouse_id', $request->warehouse_id)
                    ->first();

                if (!$inventory || $inventory->stock < $quantity) {
                    $productName = Product::find($productId)->name;
                    $errorMessages[] = 'Insufficient stock for product: ' . $productName . " || Inventory stock: " . $inventory->stock . " || Inventory ID: " . $inventory->id;
                }
            }
            
            // Si se encontraron productos con stock insuficiente, lanzar una excepción
            if (!empty($errorMessages)) {
                throw new \Exception(implode(' || ', $errorMessages));
            }

            foreach ($productsWithQuantities as $productId => $quantity) {
                // Asociar productos con las nuevas cantidades
                $sale->products()->attach($productId, ['quantity' => $quantity]);
                
                // Actualizar inventario
                app(InventoryController::class)->updateInventory($productId, $request->warehouse_id, $quantity, 'sale', auth()->id());
            }
            // Confirmar transacción
            DB::commit();

            return redirect()->route('sales.index')->with('success', 'Sale updated successfully.');
        } catch (\Exception $e) {
            // Si ocurre algún error, revertimos los cambios
            DB::rollBack();

            return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }


    public function destroy(Sale $sale)
    {
        // Obtener los productos actuales de la compra y sus cantidades previas
        $currentProducts = $sale->products()->withPivot('quantity')->get();

        // Eliminar las unidades previas de los productos en el inventario antes de actualizar
        foreach ($currentProducts as $product) {
            $oldQuantity = $product->pivot->quantity;

            // Eliminar las unidades previas del inventario (salida del inventario)
            app(InventoryController::class)->updateInventory($product->id, $sale->warehouse_id, $oldQuantity, 'restock', auth()->id());
        }

        $sale->last_update_user_id = auth()->id();
        $sale->save();

        try {
            if($sale->delete()) {
            return response()->json(['status' => 'success', 'message' => 'Success! Sale deleted successfully.'], 200);
        } else {
            return response()->json(['status' => 'error', 'message' => 'The sale could not be deleted.'], 400);
        }
        }catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'The sale could not be deleted.'], 500);
        } 
    }
}
