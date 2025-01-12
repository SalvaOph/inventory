@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <!-- Card de Productos -->
        <div class="col-md-3">
            <div class="card text-white bg-primary mb-3">
                <div class="card-header">Productos</div>
                <div class="card-body">
                    <h5 class="card-title">Total de Productos</h5>
                    <p class="card-text display-4">{{ $totalProducts }}</p>
                </div>
            </div>
        </div>

        <!-- Card de Inventarios -->
        <div class="col-md-3">
            <div class="card text-white bg-success mb-3">
                <div class="card-header">Inventarios</div>
                <div class="card-body">
                    <h5 class="card-title">Inventarios por Producto</h5>
                    <p class="card-text display-4">{{ $totalInventories }}</p>
                </div>
            </div>
        </div>

        <!-- Card de Ventas -->
        <div class="col-md-3">
            <div class="card text-white bg-warning mb-3">
                <div class="card-header">Ventas</div>
                <div class="card-body">
                    <h5 class="card-title">Total de Ventas</h5>
                    <p class="card-text display-4">{{ $totalSales }}</p>
                </div>
            </div>
        </div>

        <!-- Card de Compras -->
        <div class="col-md-3">
            <div class="card text-white bg-danger mb-3">
                <div class="card-header">Compras</div>
                <div class="card-body">
                    <h5 class="card-title">Total de Compras</h5>
                    <p class="card-text display-4">{{ $totalPurchases }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
