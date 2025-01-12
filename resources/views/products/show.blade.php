@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <h2>Product Details</h2>
            <a class="btn btn-dark" href="{{ route('products.index') }}">
                <i class="fa-solid fa-backward-step"></i> Back</a>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-header">
            <h3>{{ $product->name }}</h3>
        </div>
        <div class="card-body">
            <p><strong>Description: </strong>{{ $product->description }}</p>
            <p><strong>Price: </strong>$ {{ $product->price }}</p>
            <p><strong>Sale Price: </strong>$ {{ $product->saleprice }}</p>
        </div>
        <div class="card-footer">
            <a href="{{ route('products.edit', $product->id) }}" class="btn btn-warning">
                <i class="fa-solid fa-pen-to-square"></i> Edit</a>
            <form action="{{ route('products.destroy', $product->id) }}" method="POST" style="display: inline-block;">
                @csrf
                @method('DELETE')
                <button title="Delete" type="button" class="btn btn-danger delete-button"
                    data-url="{{ route('products.destroy', $product->id) }}" data-redirect-url="{{ route('products.index') }}"> Delete
                    <i class="fa-solid fa-trash"></i>
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

<script type="module">
    import confirmDelete from '/js/deleteconfirm.js';

    document.addEventListener('DOMContentLoaded', confirmDelete);
</script>