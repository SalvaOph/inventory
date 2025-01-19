<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GraphicsController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InventoryController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProviderController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\PurchaseDetailController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SaleDetailController;
use App\Http\Controllers\WarehouseController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ReportController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('index');
})->name('index');

Route::get('/', [DashboardController::class, 'index'])
    ->middleware('auth')
    ->name('dashboard.index');
    
Route::resource('products', ProductController::class)->middleware('auth');
Route::resource('providers', ProviderController::class)->middleware('auth');
Route::resource('inventories', InventoryController::class)->middleware('auth');
Route::resource('clients', ClientController::class)->middleware('auth');
Route::resource('sales', SaleController::class)->middleware('auth');
Route::resource('purchases', PurchaseController::class)->middleware('auth');
Route::resource('warehouses', WarehouseController::class)->middleware('auth');


Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/export/{entity}/excel', [ReportController::class, 'exportToExcel'])->name('export.excel');
Route::get('/export/{entity}/pdf', [ReportController::class, 'exportToPDF'])->name('export.pdf');

// Ruta para la página principal de gráficos
Route::get('/graphics', [GraphicsController::class, 'index'])->name('graphics.index');

// Ruta específica para los datos de ventas
Route::get('/graphics/sales', [GraphicsController::class, 'getSalesData'])->name('graphics.sales');

// Ruta específica para los datos de compras
Route::get('/graphics/purchases', [GraphicsController::class, 'getPurchasesData'])->name('graphics.purchases');

// Ruta específica para los datos de inventarios
Route::get('/graphics/inventories', [GraphicsController::class, 'getInventoryData'])->name('graphics.inventories');

// Ruta específica para los datos de product_sales
Route::get('/graphics/productsales', [GraphicsController::class, 'getProductSaleData'])->name('graphics.productsales');

// Ruta para los datos de drilldown de product_sales
Route::get('/getProductSaleDrilldownData/{productId}', [GraphicsController::class, 'getProductSaleDrilldownData'])->name('graphics.productsale.drilldown');

// Ruta específica para los datos de inventories
Route::get('/graphics/inventories', [GraphicsController::class, 'getInventoryData'])->name('graphics.inventories');

// Ruta para los datos de drilldown de inventories
Route::get('/getInventoryDrilldownData/{productId}', [GraphicsController::class, 'getInventoryDrilldownData'])->name('graphics.inventories.drilldown');

// Ruta específica para los datos de warehouses
Route::get('/graphics/warehouses', [GraphicsController::class, 'getWarehouseInventoryData'])->name('graphics.warehouses');

// Ruta para los datos de drilldown de warehouses
Route::get('/getWarehouseInventoryDrilldownData/{warehouseId}', [GraphicsController::class, 'getWarehouseInventoryDrilldownData'])->name('graphics.warehouses.drilldown');