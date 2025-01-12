@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <h2>{{ isset($product) ? 'Edit Product' : 'Create Product' }}</h2>
            <a class="btn btn-dark" href="{{ route('products.index') }}">
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

    <form action="{{ route('products.store') }}" method="POST">
    @csrf

    <!-- Campos del Producto -->
    <div class="form-group">
        <strong>Name:</strong>
        <input type="text" name="name" value="{{ old('name') }}" class="form-control" required>
    </div>
    <div class="form-group">
        <strong>Description:</strong>
        <textarea class="form-control" name="description" required>{{ old('description') }}</textarea>
    </div>
    <div class="form-group">
        <strong>Price:</strong>
        <input type="number" name="price" value="{{ old('price') }}" class="form-control" required>
    </div>

    <!-- Campos del Inventario -->
    <h3>Assign Inventory</h3>
    <div class="form-group">
        <strong>Stock:</strong>
        <input type="number" name="stock" value="{{ old('stock') }}" class="form-control" required>
    </div>
    <div class="form-group">
        <strong>Warehouse Location:</strong>
        <select name="warehouse_id" id="warehouse_select" class="form-control">
            @foreach ($warehouses as $warehouse)
                <option value="{{ $warehouse->id }}" 
                    {{ isset($inventory) && $inventory->warehouse_id == $warehouse->id ? 'selected' : '' }}>
                    {{ $warehouse->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="form-group mt-2">
        <button type="submit" class="btn btn-success">
        <i class="fa-solid fa-floppy-disk"></i> Create Product and Inventory</button>
    </div>
</form>


</div>
@endsection
