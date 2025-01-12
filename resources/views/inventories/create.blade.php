@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <h2>{{ isset($inventory) ? 'Edit Inventory' : 'Create Inventory' }}</h2>
            <a class="btn btn-dark" href="{{ route('inventories.index') }}">
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

    <form action="{{ isset($inventory) ? route('inventories.update', $inventory->id) : route('inventories.store') }}" method="POST">
        @csrf
        @if(isset($inventory)) @method('PUT') @endif

        <div class="form-group">
            <strong>Product:</strong>
            <select name="product_id" id="products_select" class="form-control">
                @foreach ($products as $product)
                <option value="{{ $product->id }}"
                    {{ isset($inventory) && $inventory->product_id == $product->id ? 'selected' : '' }}>
                    {{ $product->name }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <strong>Stock:</strong>
            <input type="number" name="stock" value="{{ isset($inventory) ? $inventory->stock : '' }}" class="form-control">
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
                <i class="fa-solid fa-floppy-disk"></i> {{ isset($inventory) ? 'Update' : 'Create' }}</button>
        </div>
    </form>

</div>
@endsection