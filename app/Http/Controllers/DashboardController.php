<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Inventory;
use App\Models\Sale;
use App\Models\Purchase;
use App\Models\Warehouse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Total de productos
        $totalProducts = Product::count();

        // Total de ventas
        $totalSales = Sale::count();

        // Total de compras
        $totalPurchases = Purchase::count();

        // Total de inventarios por producto (sumando el stock en todas las warehouses)
        $totalInventories = Inventory::select('product_id', DB::raw('SUM(stock) as total_stock'))
            ->groupBy('product_id')
            ->get()
            ->mapWithKeys(function ($inventory) {
                $product = Product::find($inventory->product_id);
                return [$product->name => $inventory->total_stock];
            });


        // Total de productos por warehouse (sumando el stock total de cada warehouse)
        $totalProductsByWarehouse = Inventory::select('warehouse_id', DB::raw('SUM(stock) as total_stock'))
            ->groupBy('warehouse_id')
            ->get()
            ->mapWithKeys(function ($inventory) {
                $warehouse = Warehouse::find($inventory->warehouse_id);
                return [$warehouse->name => $inventory->total_stock];
            });

        $inventories = Inventory::all();

        return view('dashboard.index', compact('totalProducts', 'totalInventories', 'totalSales', 'totalPurchases', 'totalProductsByWarehouse', 'inventories'));
    }
}
