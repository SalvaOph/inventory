@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <h2>Sale Details</h2>
            <a class="btn btn-dark" href="{{ route('sales.index') }}">
                <i class="fa-solid fa-backward-step"></i> Back</a>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-header">
            <h3>{{ $sale->client->name }}</h3>
        </div>
        <div class="card-body">
            <p><strong>Date: </strong>{{ \Carbon\Carbon::parse($sale->date)->translatedFormat('D, d M Y') }}</p>
            <p><strong>Total: </strong>$ {{ number_format($sale->total, 2) }}</p>
            <p><strong>Warehouse: </strong>{{ isset($warehouse) ? $warehouse->name : "There is no warehouse assigned" }}</p>

            <h4>Products</h4>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Product Name</th>
                        <th>Unit Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $i = 0;
                    @endphp
                    @foreach ($products as $product)
                    <tr>
                        <td>{{ ++$i }}</td>
                        <td>{{ $product->name }}</td>
                        <td>$ {{ number_format((float) $product->saleprice, 2) }}</td>
                        <td>{{ $product->pivot->quantity }}</td>
                        <td>$ {{ number_format((float) $product->saleprice * (float) $product->pivot->quantity, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            <a href="{{ route('sales.edit', $sale->id) }}" class="btn btn-warning">
                <i class="fa-solid fa-pen-to-square"></i> Edit</a>
            <form action="{{ route('sales.destroy', $sale->id) }}" method="POST" style="display: inline-block;">
                @csrf
                @method('DELETE')
                <button title="Delete" type="button" class="btn btn-danger delete-button"
                    data-url="{{ route('sales.destroy', $sale->id) }}" data-redirect-url="{{ route('sales.index') }}"> Delete
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