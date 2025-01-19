<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Inventory;
use App\Models\Sale;
use App\Models\Purchase;
use App\Models\Warehouse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GraphicsController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        return view('graphics.index');
    }

    public function getSalesData()
    {
        // Agrupa las ventas por fecha y suma los totales
        $sales = Sale::select(DB::raw('DATE(date) as date'), DB::raw('SUM(total) as total'))
            ->groupBy(DB::raw('DATE(date)'))
            ->orderBy('date', 'ASC')
            ->get();

        $points = [];
        foreach ($sales as $sale) {
            $points[] = [
                'name' => $sale->date,
                'y' => floatval($sale->total)
            ];
        }

        return view('graphics.sales', ["data" => json_encode($points)]);
    }

    public function getPurchasesData()
    {
        // Agrupa las ventas por fecha y suma los totales
        $purchases = Purchase::select(DB::raw('DATE(date) as date'), DB::raw('SUM(total) as total'))
            ->groupBy(DB::raw('DATE(date)'))
            ->orderBy('date', 'ASC')
            ->get();

        $points = [];
        foreach ($purchases as $purchase) {
            $points[] = [
                'name' => $purchase->date,
                'y' => floatval($purchase->total)
            ];
        }

        return view('graphics.purchases', ["data" => json_encode($points)]);
    }

    public function getProductSaleData()
    {
        // Join the sales, products, and pivot table to get the product count
        $productSales = DB::table('product_sale')
            ->join('products', 'product_sale.product_id', '=', 'products.id')
            ->join('sales', 'product_sale.sale_id', '=', 'sales.id')
            ->select('products.id', 'products.name', DB::raw('SUM(product_sale.quantity) as total_sales'))
            ->groupBy('products.id', 'products.name')
            ->orderBy('total_sales', 'DESC')
            ->get();

        $points = [];
        foreach ($productSales as $productSale) {
            $points[] = [
                'id' => $productSale->id,
                'name' => $productSale->name,
                'y' => intval($productSale->total_sales)
            ];
        }

        return view('graphics.productsales', ["data" => json_encode($points)]);
    }

    public function getProductSaleDrilldownData($productId)
    {
        // Query to get the total sales of the selected product per date
        $productSales = DB::table('product_sale')
            ->join('sales', 'product_sale.sale_id', '=', 'sales.id')
            ->select(DB::raw('DATE(sales.date) as date'), DB::raw('SUM(product_sale.quantity) as total_sales'))
            ->where('product_sale.product_id', $productId)
            ->groupBy(DB::raw('DATE(sales.date)'))
            ->orderBy('date', 'ASC')
            ->get();

        $points = [];
        foreach ($productSales as $productSale) {
            $points[] = [
                'name' => $productSale->date,
                'y' => intval($productSale->total_sales)
            ];
        }

        return response()->json($points);
    }

    public function getInventoryData()
    {
        // Query to get the total inventory of each product summing up the inventory in each warehouse
        $inventories = Inventory::select('product_id', DB::raw('SUM(stock) as total_stock'))
            ->groupBy('product_id')
            ->orderBy('total_stock', 'DESC')
            ->get();

        $points = [];
        foreach ($inventories as $inventory) {
            $points[] = [
                'id' => $inventory->product_id,
                'name' => Product::find($inventory->product_id)->name,
                'y' => intval($inventory->total_stock)
            ];
        }

        return view('graphics.inventories', ["data" => json_encode($points)]);
    }

    public function getInventoryDrilldownData($productId)
    {
        // Query to get the inventory of the selected product per warehouse
        $inventories = Inventory::select('warehouse_id', DB::raw('SUM(stock) as total_stock'))
            ->where('product_id', $productId)
            ->groupBy('warehouse_id')
            ->orderBy('total_stock', 'DESC')
            ->get();

        $points = [];
        foreach ($inventories as $inventory) {
            $points[] = [
                'name' => Warehouse::find($inventory->warehouse_id)->name,
                'y' => intval($inventory->total_stock)
            ];
        }

        return response()->json($points);
    }

    public function getWarehouseInventoryData()
    {
        $warehouseinventories = DB::table('inventories')
            ->select('warehouse_id', DB::raw('SUM(stock) as total_stock'))
            ->groupBy('warehouse_id')
            ->orderBy('warehouse_id', 'ASC')
            ->get();

        $points = [];
        $warehouses = Warehouse::whereIn('id', $warehouseinventories->pluck('warehouse_id'))->get()->keyBy('id');
        foreach ($warehouseinventories as $warehouseinventory) {
            $points[] = [
                'id' => $warehouseinventory->warehouse_id,
                'name' => $warehouses[$warehouseinventory->warehouse_id]->name ?? 'Unknown',
                'y' => intval($warehouseinventory->total_stock),
            ];
        }

        return view('graphics.warehouses', ["data" => json_encode($points)]);
    }


    public function getWarehouseInventoryDrilldownData($warehouseId)
    {
        $warehouseinventories = Inventory::select('product_id', DB::raw('SUM(stock) as total_stock'))
            ->where('warehouse_id', $warehouseId)
            ->groupBy('product_id')
            ->orderBy('total_stock', 'DESC')
            ->get();

        $points = [];
        foreach ($warehouseinventories as $warehouseinventory) {
            $points[] = [
                'name' => Product::find($warehouseinventory->product_id)->name,
                'y' => intval($warehouseinventory->total_stock)
            ];
        }

        return response()->json($points);
    }
}
