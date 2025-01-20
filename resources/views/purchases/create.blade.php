@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <h2>{{ isset($purchase) ? 'Edit Purchase' : 'Create Purchase' }}</h2>
            <a class="btn btn-dark" href="{{ route('purchases.index') }}">
                <i class="fa-solid fa-backward-step"></i> Back</a>
        </div>
    </div>

    @if ($errors->any())
    <div class="alert alert-danger">
        <strong>Whoops!</strong> There were some problems with your input.<br><br>
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ isset($purchase) ? route('purchases.update', $purchase->id) : route('purchases.store') }}" method="POST">
        @csrf
        @if(isset($purchase)) @method('PUT') @endif

        <!-- Proveedor -->
        <div class="form-group">
            <strong>Provider:</strong>
            <select name="provider_id" class="form-control">
                @foreach ($providers as $provider)
                <option value="{{ $provider->id }}" {{ (isset($purchase) && $purchase->provider_id == $provider->id) ? 'selected' : '' }}>{{ $provider->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Producto -->
        <div class="form-group">
            <strong>Product:</strong>
            <select id="products_select" class="form-control">
                @foreach ($products as $product)
                <option value="{{ $product->id }}">{{ $product->name }}</option>
                @endforeach
            </select><br>
            <button type="button" id="add_product_btn" class="btn btn-primary">
                <i class="fa-solid fa-circle-plus"></i> Add</button>
        </div>

        <!-- Tabla de productos agregados -->
        <table id="products_table" class="table table-bordered mt-2">
            <thead>
                <th>Name</th>
                <th>Unit Price</th>
                <th>Amount</th>
                <th>Total</th>
                <th>Action</th>
            </thead>
            <tbody></tbody>
        </table>

        <!-- Total de la compra -->
        <div id="total_purchase" class="form-group">
            <strong>Total</strong>
            <p id="total_p">$ 0.00</p>
        </div>

        <!-- Inputs ocultos para enviar los arrays -->
        <div id="hidden_inputs" class="form-group"></div>

        <!-- Fecha -->
        <div class="form-group mt-2">
            <strong>Date:</strong>
            <input type="date" name="date" value="{{ isset($purchase) ? $purchase->date : '' }}" class="form-control">
        </div>

        <!-- Total -->
        <input class="form-control mt-2" type="hidden" name="total" id="total">

        <!-- Almacén -->
        <div class="form-group">
            <strong>Warehouse:</strong>
            <select name="warehouse_id" class="form-control">
                @foreach ($warehouses as $warehouse)
                <option value="{{ $warehouse->id }}" {{ (isset($purchase) && $purchase->warehouse_id == $warehouse->id) ? 'selected' : '' }}>{{ $warehouse->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Botón de submit -->
        <div class="form-group mt-2">
            <button type="submit" class="btn btn-success">
                <i class="fa-solid fa-floppy-disk"></i> {{ isset($purchase) ? 'Update' : 'Create' }}</button>
        </div>
    </form>
</div>
@endsection


<script type="module">
    const products = <?php echo $products; ?>;
    const purchase = {};

    document.getElementById('add_product_btn').addEventListener('click', () => window.addProduct(products, purchase));
</script>