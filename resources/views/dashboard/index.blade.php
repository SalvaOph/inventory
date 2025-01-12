@extends('layouts.app')

@section('content')
<div class="container overflow-hidden text-center">
    <div class="row g-2">

        <!-- Card de Compras -->
        <div class="col-6">
            <div class="p-3">
                <div class="">
                    <div class="card text-white bg-danger mb-3">
                        <div class="card-header"><a class="text-white text-decoration-none" href="{{ route('graphics.purchases') }}">Compras</a></div>
                        <div class="card-body">
                            <h5 class="card-title">Total de Compras</h5>
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
                        <div class="card-header"><a class="text-white text-decoration-none" href="{{ route('graphics.sales') }}">Ventas</a></div>
                        <div class="card-body">
                            <h5 class="card-title">Total de Ventas</h5>
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
                        <div class="card-header"><a class="text-white text-decoration-none" href="{{ route('graphics.productsales') }}">Productos</a></div>
                        <div class="card-body">
                            <h5 class="card-title">Total de Productos</h5>
                            <p class="card-text display-4">{{ $totalProducts }}</p>
                        </div>
                    </div>
                </div>
                <div class="">
                    <div class="card text-white bg-warning mb-3">
                        <div class="card-header"><a class="text-white text-decoration-none" href="{{ route('warehouses.index') }}">Total Productos por Warehouse</a></div>
                        <div class="card-body">
                                @foreach ($totalProductsByWarehouse as $warehouseName => $totalStock)
                                <div class="p-2 text-light-emphasis bg-light-subtle border border-light-subtle" color="$yellow-100">
                                    <strong>{{ $warehouseName }}: {{ $totalStock }} unidades</strong>
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
                        <div class="card-header"><a class="text-white text-decoration-none" href="{{ route('graphics.inventories') }}">Inventarios por Producto</a></div>
                        <div class="card-body">
                            @foreach($totalInventories as $productName => $totalStock)
                            <div class="p-2 text-light-emphasis bg-light-subtle border border-light-subtle">
                                <strong>{{ $productName }} || Total de unidades: {{ $totalStock ?? 0 }}</strong>
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