@extends('layouts.app')

@section('content')
<div class="container overflow-hidden text-center">
    <div class="row g-2">

        <!-- Card de Compras -->
        <div class="col-6">
            <div class="p-3">
                <div class="">
                    <div class="card text-white bg-danger mb-3">
                        <div class="card-header"><a class="text-white text-decoration-none" href="{{ route('graphics.purchases') }}">Purchases</a></div>
                        <div class="card-body">
                            <h5 class="card-title">Total Purchases</h5>
                            <p class="card-text display-4">{{ $totalPurchases }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card de Ventas -->
        <div class="col-6">
            <div class="p-3">
                <div class="">
                    <div class="card text-white bg-success mb-3">
                        <div class="card-header"><a class="text-white text-decoration-none" href="{{ route('graphics.sales') }}">Sales</a></div>
                        <div class="card-body">
                            <h5 class="card-title">Total Sales</h5>
                            <p class="card-text display-4">{{ $totalSales }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card de Productos y Warehouse -->
        <div class="col-6">
            <div class="p-3">
                <div class="">
                    <div class="card text-white bg-primary mb-3">
                        <div class="card-header"><a class="text-white text-decoration-none" href="{{ route('graphics.productsales') }}">Products</a></div>
                        <div class="card-body">
                            <h5 class="card-title">Total Products</h5>
                            <p class="card-text display-4">{{ $totalProducts }}</p>
                        </div>
                    </div>
                </div>
                <div class="">
                    <div class="card text-white bg-warning mb-3">
                        <div class="card-header"><a class="text-white text-decoration-none" href="{{ route('graphics.warehouses') }}">Total Products per Warehouse</a></div>
                        <div class="card-body">
                                @foreach ($totalProductsByWarehouse as $warehouseName => $totalStock)
                                <div class="p-2 text-light-emphasis bg-light-subtle border border-light-subtle" color="$yellow-100">
                                    <strong>{{ $warehouseName }}: {{ $totalStock }} units</strong>
                                </div>
                                @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card de Inventarios -->
        <div class="col-6">
            <div class="p-3">
                <div class="">
                    <div class="card text-white bg-secondary mb-3">
                        <div class="card-header"><a class="text-white text-decoration-none" href="{{ route('graphics.inventories') }}">Products Inventory</a></div>
                        <div class="card-body">
                            @foreach($totalInventories as $productName => $totalStock)
                            <div class="p-2 text-light-emphasis bg-light-subtle border border-light-subtle">
                                <strong>{{ $productName }} || Total stock units: {{ $totalStock ?? 0 }}</strong>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection