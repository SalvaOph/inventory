@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <h2>Inventory Details</h2>
            <a class="btn btn-dark" href="{{ route('inventories.index') }}">
                <i class="fa-solid fa-backward-step"></i> Back</a>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-header">
            <h3>{{ $inventory->product->name }}</h3>
        </div>
        <div class="card-body">
            <p><strong>Stock: </strong>{{ $inventory->stock }} units</p>
            <p><strong>Warehouse Location: </strong>{{ $inventory->warehouse ? $inventory->warehouse->name : 'Pending to assign warehouse' }}</p>
        </div>
        <div class="card-footer">
            <a href="{{ route('inventories.edit', $inventory->id) }}" class="btn btn-warning">
                <i class="fa-solid fa-pen-to-square"></i> Edit</a>
            <form action="{{ route('inventories.destroy', $inventory->id) }}" method="POST" style="display: inline-block;">
                @csrf
                @method('DELETE')
                <button title="Delete" type="button" class="btn btn-danger delete-button"
                    data-url="{{ route('inventories.destroy', $inventory->id) }}" data-redirect-url="{{ route('inventories.index') }}"> Delete
                    <i class="fa-solid fa-trash"></i>
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

<script type="module">
    document.addEventListener('DOMContentLoaded', () => window.confirmDelete());
</script>